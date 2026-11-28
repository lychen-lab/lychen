import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

/**
 * Why the camera of the scanner cannot be used: what the screen explains, before it offers the
 * manual search by label (spec: "caméra refusée ou absente : explication et recherche manuelle").
 */
export const CAMERA_PROBLEM = {
  /** The person (or their browser's settings) refused access to the camera. */
  Denied: 'denied',
  /** No camera at all, or none that fits (a laptop without a webcam). */
  NoCamera: 'no-camera',
  /** There is one, but it does not start: another app uses it, or it is unplugged. */
  Busy: 'busy',
  /** The page is not served over HTTPS (except localhost): browsers keep the camera from it. */
  Insecure: 'insecure',
  /** The browser has no camera API at all. */
  Unsupported: 'unsupported',
  Unknown: 'unknown',
} as const;

export type CameraProblem = ObjectValues<typeof CAMERA_PROBLEM>;

/** The `name` of the errors `getUserMedia` rejects with (and their older names), by problem. */
const PROBLEM_BY_ERROR_NAME: ReadonlyMap<string, CameraProblem> = new Map([
  ['NotAllowedError', CAMERA_PROBLEM.Denied],
  ['PermissionDeniedError', CAMERA_PROBLEM.Denied],
  ['NotFoundError', CAMERA_PROBLEM.NoCamera],
  ['DevicesNotFoundError', CAMERA_PROBLEM.NoCamera],
  ['OverconstrainedError', CAMERA_PROBLEM.NoCamera],
  ['ConstraintNotSatisfiedError', CAMERA_PROBLEM.NoCamera],
  ['NotReadableError', CAMERA_PROBLEM.Busy],
  ['TrackStartError', CAMERA_PROBLEM.Busy],
  ['AbortError', CAMERA_PROBLEM.Busy],
  ['SecurityError', CAMERA_PROBLEM.Insecure],
]);

/**
 * The problem an error of `getUserMedia` stands for. It reads only the error's `name`, so that it
 * works for a `DOMException` as for any object; whatever it does not know is `Unknown`, which the
 * screen explains as generally as it can. It never throws.
 */
export function classifyCameraError(error: unknown): CameraProblem {
  const name =
    typeof error === 'object' && error !== null && 'name' in error ? error.name : undefined;

  return (
    (typeof name === 'string' ? PROBLEM_BY_ERROR_NAME.get(name) : undefined) ??
    CAMERA_PROBLEM.Unknown
  );
}

/** What the page knows about its environment, before it asks for the camera. */
export interface CameraEnvironment {
  /** `window.isSecureContext`. Undefined where there is no such thing: not a reason to refuse. */
  isSecureContext?: boolean;
  /** Whether `navigator.mediaDevices?.getUserMedia` exists. */
  hasGetUserMedia: boolean;
}

/**
 * The problem that is known without asking for the camera, or `undefined` when asking is worth a
 * try. An insecure page is told apart from an old browser, as the remedy is not the same (the
 * address versus the browser): `navigator.mediaDevices` is missing in both.
 */
export function cameraProblemBeforeAsking(
  environment: CameraEnvironment,
): CameraProblem | undefined {
  if (environment.isSecureContext === false) {
    return CAMERA_PROBLEM.Insecure;
  }

  return environment.hasGetUserMedia ? undefined : CAMERA_PROBLEM.Unsupported;
}
