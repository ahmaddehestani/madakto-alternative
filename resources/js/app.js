import { createApp } from 'vue';
import App from '../vue/App.vue';
import router from './router/index';

createApp(App)
    .use(router)
    .mount('#app');
