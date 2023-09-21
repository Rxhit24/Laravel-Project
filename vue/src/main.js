import { createApp } from 'vue'
import store from './store'  //added 1.1
import router from './router'  //added2.1
import './style.css'
import './index.css'
import App from './App.vue'

createApp(App)
.use(store)              //addes 1.2
.use(router)            //added2.1
.mount('#app')
