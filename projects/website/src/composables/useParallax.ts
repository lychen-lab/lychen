import { onBeforeUnmount, onMounted, type Ref } from 'vue';

export interface UseParallaxOptions {
  /** Also track the pointer and expose `--pointer-x` / `--pointer-y` (-1..1). */
  pointer?: boolean;
}

/**
 * Drives scroll- and pointer-reactive CSS custom properties on a section element:
 *
 * - `--scroll-y`: pixels scrolled past the section top (>= 0, unitless)
 * - `--center-delta`: signed distance between the section center and the viewport
 *   center, normalized by the viewport height (~ -1..1, unitless)
 * - `--pointer-x` / `--pointer-y`: smoothed pointer position (-1..1, unitless)
 *
 * Layers consume them with `calc()`, e.g. `translate3d(0, calc(var(--scroll-y) * 0.3px), 0)`.
 * The rAF loop only runs while the section intersects the viewport, and never starts
 * on the server, in the prerendered SSG markup or for `prefers-reduced-motion` users,
 * so the page degrades to a fully static layout.
 */
export function useParallax(target: Ref<HTMLElement | null>, options: UseParallaxOptions = {}) {
  let cleanup: (() => void) | undefined;

  onMounted(() => {
    const element = target.value;
    if (!element) {
      return;
    }

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let frame = 0;
    let running = false;
    let pointerTargetX = 0;
    let pointerTargetY = 0;
    let pointerX = 0;
    let pointerY = 0;

    function setVariables() {
      const rect = element.getBoundingClientRect();
      const viewportHeight = window.innerHeight;
      const scrollY = Math.max(0, -rect.top);
      const centerDelta = (rect.top + rect.height / 2 - viewportHeight / 2) / viewportHeight;

      element.style.setProperty('--scroll-y', scrollY.toFixed(1));
      element.style.setProperty('--center-delta', centerDelta.toFixed(4));

      if (options.pointer) {
        pointerX += (pointerTargetX - pointerX) * 0.08;
        pointerY += (pointerTargetY - pointerY) * 0.08;
        element.style.setProperty('--pointer-x', pointerX.toFixed(4));
        element.style.setProperty('--pointer-y', pointerY.toFixed(4));
      }
    }

    function loop() {
      if (!running) {
        return;
      }
      setVariables();
      frame = requestAnimationFrame(loop);
    }

    function start() {
      if (running || reducedMotion.matches) {
        return;
      }
      running = true;
      frame = requestAnimationFrame(loop);
    }

    function stop() {
      running = false;
      cancelAnimationFrame(frame);
    }

    const observer = new IntersectionObserver(([entry]) => {
      if (entry?.isIntersecting) {
        start();
      } else {
        stop();
      }
    });
    observer.observe(element);

    function onPointerMove(event: MouseEvent) {
      pointerTargetX = (event.clientX / window.innerWidth) * 2 - 1;
      pointerTargetY = (event.clientY / window.innerHeight) * 2 - 1;
    }
    if (options.pointer) {
      window.addEventListener('mousemove', onPointerMove, { passive: true });
    }

    function onReducedMotionChange() {
      if (reducedMotion.matches) {
        stop();
        ['--scroll-y', '--center-delta', '--pointer-x', '--pointer-y'].forEach((name) => {
          element.style.removeProperty(name);
        });
      } else {
        start();
      }
    }
    reducedMotion.addEventListener('change', onReducedMotionChange);

    cleanup = () => {
      stop();
      observer.disconnect();
      if (options.pointer) {
        window.removeEventListener('mousemove', onPointerMove);
      }
      reducedMotion.removeEventListener('change', onReducedMotionChange);
    };
  });

  onBeforeUnmount(() => {
    cleanup?.();
  });
}
