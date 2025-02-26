import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './routes/index.js';
import 'ant-design-vue';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import axios from 'axios';
import { Button, Drawer, message, Menu, List } from 'ant-design-vue';
import store from './store/index.js';
import Antd from 'ant-design-vue'

window.axios = axios;

const app = createApp(App);
app.use(router);
app.use(Button);
app.use(Drawer);
app.use(Menu);
app.use(List);
app.use(store);
app.use(Antd)
app.mount("#app");

app.config.globalProperties.$message = message;