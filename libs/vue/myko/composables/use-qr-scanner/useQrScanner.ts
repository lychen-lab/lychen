import {
  getCurrentScope,
  onScopeDispose,
  ref,
  shallowReadonly,
  toValue,
  type MaybeRefOrGetter,
} from 'vue';

import {
  CAMERA_PROBLEM,
  type CameraProblem,
  cameraProblemBeforeAsking,
  classifyCameraError,
} from '@lychen/typescript-myko/scan/cameraProblem';
import { createBarcodeQrDetector, type QrDetector } from '@lychen/typescript-myko/scan/qrDetector';

/** What the scanner is doing, for the screen: nothing, asking for the camera, reading, or stuck. */
export const QR_SCANNER_STATE = {
  Idle: 'idle',
  Starting: 'starting',
  Scanning: 'scanning',
  /** Look at `problem`: the screen explains it and offers the manual search by label. */
  Problem: 'problem',
} as const;

export type QrScannerState = (typeof QR_SCANNER_STATE)[keyof typeof QR_SCANNER_STATE];

/** How often a frame is read: a QR code held still in front of the camera is found in a blink. */
export const QR_SCANNER_DEFAULT_INTERVAL_MS = 250;

/** How long the same content is not reported again: the code is still in front of the camera. */
export const QR_SCANNER_DEFAULT_REPEAT_DELAY_MS = 3000;

/** `HTMLVideoElement.HAVE_CURRENT_DATA`: there is a frame to read. */
const HAVE_CURRENT_DATA = 2;

export interface UseQrScannerOptions {
  /** The `<video>` the camera is shown in: a template ref. Read when `start()` is called. */
  video: MaybeRefOrGetter<HTMLVideoElement | null | undefined>;
  /** Called with the content of a QR code the camera read (`resolveScannedContent` says what it is). */
  onScan: (content: string) => void;
  /**
   * What reads the frames. Left out, the browser's own `BarcodeDetector`; where there is none, a
   * detector of another kind (a library, for Safari on iOS) is given here.
   */
  createDetector?: () => Promise<QrDetector | undefined>;
  intervalMs?: number;
  repeatDelayMs?: number;
  /** For tests: where the camera and the page's security come from, the page's own by default. */
  environment?: {
    mediaDevices?: Pick<MediaDevices, 'getUserMedia'>;
    isSecureContext?: boolean;
  };
}

/**
 * The camera of the scanner: asks for the rear camera, shows it in the `<video>`, reads a frame
 * every `intervalMs` and reports each QR code to `onScan`, once per `repeatDelayMs` for the same
 * content. A camera that cannot be used is a `problem` (`CameraProblem`), never an exception.
 *
 * `start()` must follow a tap or the screen's opening, and `stop()` releases the camera (its light
 * goes off); both are safe to call twice and in any order, and the scope's end stops it. The
 * detector is made before the camera is asked for, so that a browser that cannot read QR codes does
 * not light the camera for nothing.
 */
export function useQrScanner(options: UseQrScannerOptions) {
  const state = ref<QrScannerState>(QR_SCANNER_STATE.Idle);
  const problem = ref<CameraProblem>();

  const intervalMs = options.intervalMs ?? QR_SCANNER_DEFAULT_INTERVAL_MS;
  const repeatDelayMs = options.repeatDelayMs ?? QR_SCANNER_DEFAULT_REPEAT_DELAY_MS;
  const createDetector = options.createDetector ?? (() => createBarcodeQrDetector());

  /** Changes at each start and stop: what an older, slower start finds changed gives up. */
  let session = 0;
  let stream: MediaStream | undefined;
  let video: HTMLVideoElement | undefined;
  let timer: ReturnType<typeof setTimeout> | undefined;

  function releaseCamera(): void {
    clearTimeout(timer);
    timer = undefined;
    stream?.getTracks().forEach((track) => track.stop());
    stream = undefined;
    if (video) {
      video.srcObject = null;
      video = undefined;
    }
  }

  function fail(reason: CameraProblem): void {
    releaseCamera();
    problem.value = reason;
    state.value = QR_SCANNER_STATE.Problem;
  }

  async function start(): Promise<void> {
    if (state.value === QR_SCANNER_STATE.Starting || state.value === QR_SCANNER_STATE.Scanning) {
      return;
    }

    const mine = ++session;
    const element = toValue(options.video);
    const mediaDevices =
      options.environment && 'mediaDevices' in options.environment
        ? options.environment.mediaDevices
        : globalThis.navigator?.mediaDevices;
    const isSecureContext = options.environment?.isSecureContext ?? globalThis.isSecureContext;

    problem.value = undefined;

    const knownProblem = cameraProblemBeforeAsking({
      isSecureContext,
      hasGetUserMedia: typeof mediaDevices?.getUserMedia === 'function',
    });
    if (knownProblem !== undefined || !element || !mediaDevices) {
      fail(knownProblem ?? CAMERA_PROBLEM.Unknown);
      return;
    }

    state.value = QR_SCANNER_STATE.Starting;

    const detector = await createDetector();
    if (mine !== session) {
      return;
    }
    if (!detector) {
      fail(CAMERA_PROBLEM.Unsupported);
      return;
    }

    let opened: MediaStream;
    try {
      opened = await mediaDevices.getUserMedia({
        video: { facingMode: { ideal: 'environment' } },
        audio: false,
      });
    } catch (error) {
      if (mine === session) {
        fail(classifyCameraError(error));
      }
      return;
    }

    if (mine !== session) {
      // Stopped while the camera was being asked for: let it go at once.
      opened.getTracks().forEach((track) => track.stop());
      return;
    }

    stream = opened;
    video = element;
    element.muted = true;
    element.playsInline = true;
    element.srcObject = opened;
    try {
      await element.play();
    } catch {
      // Not the camera's fault (a blocked autoplay, an interrupted load): no better name for it.
      if (mine === session) {
        fail(CAMERA_PROBLEM.Unknown);
      }
      return;
    }
    if (mine !== session) {
      return;
    }

    state.value = QR_SCANNER_STATE.Scanning;
    scheduleNextRead(mine, element, detector);
  }

  let lastContent: string | undefined;
  let lastReportedAt = Number.NEGATIVE_INFINITY;

  function scheduleNextRead(mine: number, element: HTMLVideoElement, detector: QrDetector): void {
    timer = setTimeout(async () => {
      if (mine !== session) {
        return;
      }

      if (element.readyState >= HAVE_CURRENT_DATA) {
        try {
          const content = await detector.detect(element);
          const now = Date.now();

          if (
            mine === session &&
            content !== undefined &&
            (content !== lastContent || now - lastReportedAt >= repeatDelayMs)
          ) {
            lastContent = content;
            lastReportedAt = now;
            options.onScan(content);
          }
        } catch {
          // A frame the detector could not read is not the end of the scan: the next one will do.
        }
      }

      if (mine === session) {
        scheduleNextRead(mine, element, detector);
      }
    }, intervalMs);
  }

  function stop(): void {
    session += 1;
    releaseCamera();
    lastContent = undefined;
    lastReportedAt = Number.NEGATIVE_INFINITY;
    state.value = QR_SCANNER_STATE.Idle;
  }

  if (getCurrentScope()) {
    onScopeDispose(stop);
  }

  return { state: shallowReadonly(state), problem: shallowReadonly(problem), start, stop };
}
