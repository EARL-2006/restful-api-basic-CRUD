import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';

// Set the Sanctum token globally for Axios if it exists in localStorage
const token = localStorage.getItem('token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const app = createApp(App);
app.mount('#app');