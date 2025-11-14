import type { Preview } from '@storybook/vue3-vite';
import { setup } from '@storybook/vue3';

import { createI18n } from 'vue-i18n';
import { configDefault } from '@lychen/vue-i18n/configs/ConfigDefault';

import { ModeDecorator } from './modeDecorator';
import { themes } from 'storybook/theming';

import './storybook.css';

const i18nConfig = configDefault();
const i18n = createI18n(i18nConfig);

const preview: Preview = {
  decorators: [ModeDecorator],
  parameters: {
    darkMode: {
      dark: { ...themes.dark },
      light: { ...themes.normal },
      stylePreview: true,
    },
    controls: {
      matchers: {
        color: /(background|color)$/i,
        date: /Date$/i,
      },
    },
    docs: {
      canvas: {
        className: ``,
      },
    },
    backgrounds: {
      options: {
        default: { name: 'default', value: 'rgb(255, 255, 255)' },
        surface: { name: 'surface', value: 'var(--color-surface)' },
        'surface-container-lowest': {
          name: 'surface-container-lowest',
          value: 'var(--color-surface-container-lowest)',
        },
        'surface-container-low': {
          name: 'surface-container-low',
          value: 'var(--color-surface-container-low)',
        },
        'surface-container': { name: 'surface-container', value: 'var(--color-surface-container)' },
        'surface-container-high': {
          name: 'surface-container-high',
          value: 'var(--color-surface-container-high)',
        },
        'surface-container-highest': {
          name: 'surface-container-highest',
          value: 'var(--color-surface-container-highest)',
        },
      },
    },
  },
  initialGlobals: {
    backgrounds: { value: 'default' },
  },
  tags: ['autodocs'],
};

setup((app) => {
  app.use(i18n);
});

export default preview;
