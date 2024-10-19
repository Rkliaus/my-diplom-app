import './bootstrap';
import { createApp } from 'vue'
import App from './App.vue'
import axios from 'axios';

createApp(App).mount('#app')

// axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
