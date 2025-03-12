// import '@/bootstrap';
// import { createApp } from 'vue';
// import App from '@/App.vue';
// import router from '@/routes/index.js';
// import 'ant-design-vue';
// import 'bootstrap/dist/css/bootstrap.min.css';
// import 'bootstrap/dist/js/bootstrap.bundle.min.js';
// import axios from 'axios';
// import { Button, Drawer, message, Menu, List } from 'ant-design-vue';
// import store from '@/store/index.js';
// import Antd from 'ant-design-vue'
// import ToastPlugin from 'vue-toast-notification';
// import 'vue-toast-notification/dist/theme-bootstrap.css';

// window.axios = axios;

// const app = createApp(App);
// app.use(router);
// app.use(Button);
// app.use(Drawer);
// app.use(Menu);
// app.use(List);
// app.use(store);
// app.use(Antd)
// app.use(ToastPlugin);
// app.mount("#app");

// app.config.globalProperties.$message = message;


import '@/bootstrap';
import { createApp, onMounted, watch } from 'vue';
import App from '@/App.vue';
import router from '@/routes/index.js';
import 'ant-design-vue';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import axios from 'axios';
import { Button, Drawer, message, Menu, List } from 'ant-design-vue';
import store from '@/store/index.js';
import Antd from 'ant-design-vue';
import ToastPlugin from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';

window.axios = axios;

const app = createApp(App);
app.use(router);
app.use(Button);
app.use(Drawer);
app.use(Menu);
app.use(List);
app.use(store);
app.use(Antd);
app.use(ToastPlugin);
app.mount("#app");

app.config.globalProperties.$message = message;

watch(
    () => store.getters["auth/user"], 
    (user) => {
      if (user && user.id) {
        window.Echo.private(`orders.${user.id}`)
            .listen('OrderStatusChanged', (e) => {
                console.log("user.id", user.id);
                console.log("e", e.message);
                alert(e.message);
            });
      }
    },
    { immediate: true }
  );