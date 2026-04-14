import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    } else {
        delete config.headers.Authorization;
    }
    return config;
});

// Shared helper — removes token from localStorage and clears the Axios default header.
export function clearAuth() {
    localStorage.removeItem('token');
    delete axios.defaults.headers.common['Authorization'];
}

window.axios = axios;

export default axios;
