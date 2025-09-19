import { createApp } from 'vue'
import App from './components/App.vue'
import axios from 'axios'

// Configurar axios
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

createApp(App).mount('#app')
