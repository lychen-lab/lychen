import { describe, expect, it } from 'vitest';

import { CAMERA_PROBLEM, cameraProblemBeforeAsking, classifyCameraError } from './cameraProblem';

describe('classifyCameraError', () => {
  it.each([
    ['NotAllowedError', CAMERA_PROBLEM.Denied],
    ['PermissionDeniedError', CAMERA_PROBLEM.Denied],
    ['NotFoundError', CAMERA_PROBLEM.NoCamera],
    ['DevicesNotFoundError', CAMERA_PROBLEM.NoCamera],
    ['OverconstrainedError', CAMERA_PROBLEM.NoCamera],
    ['NotReadableError', CAMERA_PROBLEM.Busy],
    ['TrackStartError', CAMERA_PROBLEM.Busy],
    ['AbortError', CAMERA_PROBLEM.Busy],
    ['SecurityError', CAMERA_PROBLEM.Insecure],
  ])('reads %s as %s', (name, problem) => {
    expect(classifyCameraError({ name })).toBe(problem);
  });

  it('reads a DOMException by its name', () => {
    expect(classifyCameraError(new DOMException('Permission denied', 'NotAllowedError'))).toBe(
      CAMERA_PROBLEM.Denied,
    );
  });

  it('reads an Error given a name', () => {
    const error = new Error('no camera');
    error.name = 'NotFoundError';

    expect(classifyCameraError(error)).toBe(CAMERA_PROBLEM.NoCamera);
  });

  it.each([
    ['an error it does not know', new TypeError('boom')],
    ['an error without a name', {}],
    ['a name that is not text', { name: 42 }],
    ['a string', 'NotAllowedError'],
    ['null', null],
    ['undefined', undefined],
  ])('does not know %s, and does not throw', (_what, error) => {
    expect(classifyCameraError(error)).toBe(CAMERA_PROBLEM.Unknown);
  });

  it('does not take an inherited property of Object for a problem', () => {
    expect(classifyCameraError({ name: 'toString' })).toBe(CAMERA_PROBLEM.Unknown);
    expect(classifyCameraError({ name: 'constructor' })).toBe(CAMERA_PROBLEM.Unknown);
  });
});

describe('cameraProblemBeforeAsking', () => {
  it('lets a secure page with the camera API ask', () => {
    expect(
      cameraProblemBeforeAsking({ isSecureContext: true, hasGetUserMedia: true }),
    ).toBeUndefined();
  });

  it('lets a page ask where the browser does not say whether it is secure', () => {
    expect(cameraProblemBeforeAsking({ hasGetUserMedia: true })).toBeUndefined();
  });

  it('says an insecure page is one, whether or not the API is there', () => {
    expect(cameraProblemBeforeAsking({ isSecureContext: false, hasGetUserMedia: false })).toBe(
      CAMERA_PROBLEM.Insecure,
    );
    expect(cameraProblemBeforeAsking({ isSecureContext: false, hasGetUserMedia: true })).toBe(
      CAMERA_PROBLEM.Insecure,
    );
  });

  it('says a browser without the camera API does not support it', () => {
    expect(cameraProblemBeforeAsking({ isSecureContext: true, hasGetUserMedia: false })).toBe(
      CAMERA_PROBLEM.Unsupported,
    );
  });
});
