import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';

axios.interceptors.response.use(
    response => response,
    error => {
        const status = error?.response?.status;
        if (status === 401) {
            localStorage.removeItem('token');
            router.push('/401');
        } else if (status === 403) {
            router.push('/403');
        } else if (status === 404) {
            router.push('/404');
        } else if (status >= 500) {
            router.push('/500');
        }
        return Promise.reject(error);
    }
);

createApp(App).use(router).mount('#app');
