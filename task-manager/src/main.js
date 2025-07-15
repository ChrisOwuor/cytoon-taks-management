import { createApp } from "vue";
import "./style.css";
import App from "./App.vue";
import router from "./router/Index";
import { createPinia } from "pinia";

createApp(App)
  .use(createPinia()) // ✅ Use Pinia
  .use(router)
  .mount("#app");
