import { createApp } from 'vue'
import App from './App.vue'
// Import CSS & JS Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap' // JS untuk komponen seperti Collapse, Modal, Dropdown, dll
import '@fortawesome/fontawesome-free/css/all.min.css'
import routes from './routes'

const app = createApp(App)
app.use(routes)
app.mount('#app')
