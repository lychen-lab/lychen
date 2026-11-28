import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { effectScope } from 'vue';

import { CAMERA_PROBLEM } from '@lychen/typescript-myko/scan/cameraProblem';
import type { QrDetector } from '@lychen/typescript-myko/scan/qrDetector';

import {
  QR_SCANNER_DEFAULT_INTERVAL_MS,
  QR_SCANNER_DEFAULT_REPEAT_DELAY_MS,
  QR_SCANNER_STATE,
  useQrScanner,
  type UseQrScannerOptions,
} from './useQrScanner';

function fakeVideo(readyState = 4) {
  return {
    srcObject: null as unknown,
    readyState,
    muted: false,
    playsInline: false,
    play: vi.fn(async () => {}),
  };
}

function fakeStream() {
  const track = { stop: vi.fn() };

  return { track, stream: { getTracks: () => [track] } as unknown as MediaStream };
}

function fakeDetector(...contents: (string | undefined | Error)[]) {
  const queue = [...contents];
  const detect = vi.fn(async () => {
    const next = queue.length > 1 ? queue.shift() : queue[0];
    if (next instanceof Error) {
      throw next;
    }
    return next;
  });

  return { detector: { detect } as QrDetector, detect };
}

function setup(overrides: Partial<UseQrScannerOptions> = {}, detector = fakeDetector('B')) {
  const video = fakeVideo();
  const { stream, track } = fakeStream();
  const getUserMedia = vi.fn(async () => stream);
  const onScan = vi.fn();
  const scope = effectScope();
  const scanner = scope.run(() =>
    useQrScanner({
      video: () => video as unknown as HTMLVideoElement,
      onScan,
      createDetector: async () => detector.detector,
      environment: { mediaDevices: { getUserMedia }, isSecureContext: true },
      ...overrides,
    }),
  )!;

  return { scanner, scope, video, track, getUserMedia, onScan, detect: detector.detect };
}

describe('useQrScanner', () => {
  beforeEach(() => {
    vi.useFakeTimers();
  });

  afterEach(() => {
    vi.useRealTimers();
  });

  it('is idle until it is started', () => {
    const { scanner, getUserMedia } = setup();

    expect(scanner.state.value).toBe(QR_SCANNER_STATE.Idle);
    expect(scanner.problem.value).toBeUndefined();
    expect(getUserMedia).not.toHaveBeenCalled();
  });

  it('asks for the rear camera, shows it in the video and starts scanning', async () => {
    const { scanner, video, getUserMedia, track } = setup();

    await scanner.start();

    expect(getUserMedia).toHaveBeenCalledWith({
      video: { facingMode: { ideal: 'environment' } },
      audio: false,
    });
    expect(video.srcObject).not.toBeNull();
    expect(video.muted).toBe(true);
    expect(video.playsInline).toBe(true);
    expect(video.play).toHaveBeenCalledOnce();
    expect(scanner.state.value).toBe(QR_SCANNER_STATE.Scanning);
    expect(track.stop).not.toHaveBeenCalled();
  });

  it('reports the code a frame holds', async () => {
    const { scanner, onScan, detect, video } = setup();

    await scanner.start();
    await vi.advanceTimersByTimeAsync(QR_SCANNER_DEFAULT_INTERVAL_MS);

    expect(detect).toHaveBeenCalledWith(video);
    expect(onScan).toHaveBeenCalledExactlyOnceWith('B');
  });

  it('does not report the same code again while it is still in front of the camera', async () => {
    const { scanner, onScan } = setup();

    await scanner.start();
    await vi.advanceTimersByTimeAsync(QR_SCANNER_DEFAULT_INTERVAL_MS * 4);
    expect(onScan).toHaveBeenCalledTimes(1);

    await vi.advanceTimersByTimeAsync(QR_SCANNER_DEFAULT_REPEAT_DELAY_MS);
    expect(onScan.mock.calls.length).toBeGreaterThanOrEqual(2);
  });

  it('reports a different code at once', async () => {
    const { scanner, onScan } = setup({}, fakeDetector('A', 'B'));

    await scanner.start();
    await vi.advanceTimersByTimeAsync(QR_SCANNER_DEFAULT_INTERVAL_MS * 2);

    expect(onScan.mock.calls.map(([content]) => content)).toEqual(['A', 'B']);
  });

  it('reports nothing for a frame without a code', async () => {
    const { scanner, onScan } = setup({}, fakeDetector(undefined));

    await scanner.start();
    await vi.advanceTimersByTimeAsync(QR_SCANNER_DEFAULT_INTERVAL_MS * 3);

    expect(onScan).not.toHaveBeenCalled();
  });

  it('keeps scanning after a frame the detector could not read', async () => {
    const { scanner, onScan } = setup({}, fakeDetector(new Error('bad frame'), 'B'));

    await scanner.start();
    await vi.advanceTimersByTimeAsync(QR_SCANNER_DEFAULT_INTERVAL_MS * 2);

    expect(onScan).toHaveBeenCalledWith('B');
    expect(scanner.state.value).toBe(QR_SCANNER_STATE.Scanning);
  });

  it('waits for the video to have a frame before reading', async () => {
    const detector = fakeDetector('B');
    const video = fakeVideo(0);
    const { scanner, onScan, detect } = setup(
      { video: () => video as unknown as HTMLVideoElement },
      detector,
    );

    await scanner.start();
    await vi.advanceTimersByTimeAsync(QR_SCANNER_DEFAULT_INTERVAL_MS * 2);
    expect(detect).not.toHaveBeenCalled();

    video.readyState = 4;
    await vi.advanceTimersByTimeAsync(QR_SCANNER_DEFAULT_INTERVAL_MS);
    expect(onScan).toHaveBeenCalledWith('B');
  });

  it('follows the interval it is given', async () => {
    const { scanner, onScan } = setup({ intervalMs: 1000 });

    await scanner.start();
    await vi.advanceTimersByTimeAsync(999);
    expect(onScan).not.toHaveBeenCalled();

    await vi.advanceTimersByTimeAsync(1);
    expect(onScan).toHaveBeenCalledOnce();
  });

  describe('when the camera cannot be used', () => {
    it.each([
      ['NotAllowedError', CAMERA_PROBLEM.Denied],
      ['NotFoundError', CAMERA_PROBLEM.NoCamera],
      ['NotReadableError', CAMERA_PROBLEM.Busy],
      ['SomethingElse', CAMERA_PROBLEM.Unknown],
    ])('says %s is the problem %s', async (name, expected) => {
      const { scanner } = setup({
        environment: {
          isSecureContext: true,
          mediaDevices: {
            getUserMedia: vi.fn(async () => {
              throw Object.assign(new Error('refused'), { name });
            }),
          },
        },
      });

      await scanner.start();

      expect(scanner.state.value).toBe(QR_SCANNER_STATE.Problem);
      expect(scanner.problem.value).toBe(expected);
    });

    it('does not ask for the camera of an insecure page', async () => {
      const { scanner, getUserMedia } = setup({
        environment: { isSecureContext: false, mediaDevices: { getUserMedia: vi.fn() } },
      });

      await scanner.start();

      expect(scanner.problem.value).toBe(CAMERA_PROBLEM.Insecure);
      expect(getUserMedia).not.toHaveBeenCalled();
    });

    it('says a browser without the camera API does not support it', async () => {
      const { scanner } = setup({
        environment: { isSecureContext: true, mediaDevices: undefined },
      });

      await scanner.start();

      expect(scanner.state.value).toBe(QR_SCANNER_STATE.Problem);
      expect(scanner.problem.value).toBe(CAMERA_PROBLEM.Unsupported);
    });

    it('does not light the camera when nothing can read the QR codes', async () => {
      const { scanner, getUserMedia } = setup({ createDetector: async () => undefined });

      await scanner.start();

      expect(scanner.problem.value).toBe(CAMERA_PROBLEM.Unsupported);
      expect(getUserMedia).not.toHaveBeenCalled();
    });

    it('lets the camera go when the video cannot play', async () => {
      const video = fakeVideo();
      video.play.mockRejectedValue(new Error('interrupted'));
      const { scanner, track } = setup({ video: () => video as unknown as HTMLVideoElement });

      await scanner.start();

      expect(scanner.problem.value).toBe(CAMERA_PROBLEM.Unknown);
      expect(track.stop).toHaveBeenCalledOnce();
      expect(video.srcObject).toBeNull();
    });

    it('has no video to show the camera in', async () => {
      const { scanner, getUserMedia } = setup({ video: () => null });

      await scanner.start();

      expect(scanner.state.value).toBe(QR_SCANNER_STATE.Problem);
      expect(getUserMedia).not.toHaveBeenCalled();
    });

    it('forgets the problem when started again', async () => {
      let refuse = true;
      const { scanner } = setup({
        environment: {
          isSecureContext: true,
          mediaDevices: {
            getUserMedia: vi.fn(async () => {
              if (refuse) {
                throw Object.assign(new Error('refused'), { name: 'NotAllowedError' });
              }
              return fakeStream().stream;
            }),
          },
        },
      });

      await scanner.start();
      expect(scanner.problem.value).toBe(CAMERA_PROBLEM.Denied);

      refuse = false;
      await scanner.start();

      expect(scanner.problem.value).toBeUndefined();
      expect(scanner.state.value).toBe(QR_SCANNER_STATE.Scanning);
    });
  });

  describe('stop', () => {
    it('lets the camera go and reads no more', async () => {
      const { scanner, track, video, detect } = setup();

      await scanner.start();
      scanner.stop();
      const reads = detect.mock.calls.length;
      await vi.advanceTimersByTimeAsync(QR_SCANNER_DEFAULT_INTERVAL_MS * 5);

      expect(track.stop).toHaveBeenCalledOnce();
      expect(video.srcObject).toBeNull();
      expect(scanner.state.value).toBe(QR_SCANNER_STATE.Idle);
      expect(detect.mock.calls.length).toBe(reads);
    });

    it('is safe to call without a start, and twice', async () => {
      const { scanner, track } = setup();

      scanner.stop();
      await scanner.start();
      scanner.stop();
      scanner.stop();

      expect(track.stop).toHaveBeenCalledOnce();
    });

    it('lets the camera go when it comes after a stop that came first', async () => {
      const { stream, track } = fakeStream();
      let answer: ((stream: MediaStream) => void) | undefined;
      const { scanner, video } = setup({
        environment: {
          isSecureContext: true,
          mediaDevices: {
            getUserMedia: vi.fn(
              () =>
                new Promise<MediaStream>((resolve) => {
                  answer = resolve;
                }),
            ),
          },
        },
      });

      const starting = scanner.start();
      await vi.advanceTimersByTimeAsync(0);
      expect(scanner.state.value).toBe(QR_SCANNER_STATE.Starting);

      scanner.stop();
      answer?.(stream);
      await starting;

      expect(track.stop).toHaveBeenCalledOnce();
      expect(video.srcObject).toBeNull();
      expect(scanner.state.value).toBe(QR_SCANNER_STATE.Idle);
    });

    it('is done when the scope that made the scanner ends', async () => {
      const { scanner, scope, track } = setup();

      await scanner.start();
      scope.stop();

      expect(track.stop).toHaveBeenCalledOnce();
      expect(scanner.state.value).toBe(QR_SCANNER_STATE.Idle);
    });

    it('can start again after a stop', async () => {
      const { scanner, getUserMedia } = setup();

      await scanner.start();
      scanner.stop();
      await scanner.start();

      expect(getUserMedia).toHaveBeenCalledTimes(2);
      expect(scanner.state.value).toBe(QR_SCANNER_STATE.Scanning);
    });

    it('reports a code again after a stop and a start', async () => {
      const { scanner, onScan } = setup();

      await scanner.start();
      await vi.advanceTimersByTimeAsync(QR_SCANNER_DEFAULT_INTERVAL_MS);
      scanner.stop();
      await scanner.start();
      await vi.advanceTimersByTimeAsync(QR_SCANNER_DEFAULT_INTERVAL_MS);

      expect(onScan).toHaveBeenCalledTimes(2);
    });
  });

  it('does not start twice', async () => {
    const { scanner, getUserMedia } = setup();

    await Promise.all([scanner.start(), scanner.start()]);
    await scanner.start();

    expect(getUserMedia).toHaveBeenCalledOnce();
  });
});
