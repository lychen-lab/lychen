/* eslint-disable @typescript-eslint/no-explicit-any */
import { onMounted, onUnmounted, ref, watch } from 'vue';
import type { Decorator } from '@storybook/vue3';
import TooltipProvider from '@lychen/vue-components-core/tooltip/TooltipProvider.vue';

export function ModeDecorator(story: any, context: any): any {
  return {
    components: { Story: story(), TooltipProvider },
    template: `
      <TooltipProvider>
        <div class="grid grid-cols-[1fr_1fr] gap-4 story-wrapper">
          <div class="flex flex-row items-center justify-center bg-surface text-on-surface p-4 rounded-md relative min-h-[100px]" data-theme="light">
            <Story />
          </div>
          <div class="flex flex-row items-center justify-center bg-surface text-on-surface p-4 rounded-md relative min-h-[100px]" data-theme="dark">
            <Story />
          </div>
        </div>
      </TooltipProvider>
    `,
    setup() {},
  };
}
