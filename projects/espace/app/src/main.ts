import './stylesheet/main.css';

import App from './App.vue';
import routes from './router/routes';
import { ViteSSGScaffold } from '@lychen/vite-ssg/SSGScaffold';

export const createApp = ViteSSGScaffold(App, routes);
