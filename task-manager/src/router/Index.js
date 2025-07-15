import { createRouter, createWebHistory } from "vue-router";


import Dashboard from "../views/Dashboard.vue";
import HomeView from "../views/HomeView.vue";
import AllTasks from "../views/dashboard/AllTasks.vue";
import UserList from "../views/dashboard/UserList.vue";
import MyTasks from "../views/dashboard/MyTasks.vue";
import Profile from "../views/dashboard/Profile.vue";
import Login from "../views/auth/Login.vue";
import { useAuthStore } from "../stores/auth";

const routes = [
  {
    path: "/login",
    name: "Login",
    component: Login,
    meta: { guestOnly: true },
  },
  {
    path: "/",
    component: Dashboard,
    meta: { requiresAuth: true },
    children: [
      {
        path: "",
        name: "Home",
        component: HomeView,
      },
      {
        path: "tasks",
        name: "AllTasks",
        component: AllTasks,
        meta: { role: "admin" },
      },
      {
        path: "users",
        name: "UserList",
        component: UserList,
        meta: { role: "admin" },
      },
      {
        path: "my-tasks",
        name: "MyTasks",
        component: MyTasks,
        meta: { role: "user" },
      },
      {
        path: "profile",
        name: "Profile",
        component: Profile,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// 🔒 Route guard for auth and role-based access
router.beforeEach((to, from, next) => {
  const auth = useAuthStore();

  if (to.meta.requiresAuth && !auth.token) {
    return next("/login");
  }

  if (to.meta.guestOnly && auth.token) {
    return next("/");
  }

  if (to.meta.role && to.meta.role !== auth.role) {
    return next("/");
  }

  next();
});

export default router;
