import { defineStore } from "pinia";
import axios from "axios";
import { jwtDecode } from "jwt-decode";
export const useAuthStore = defineStore("auth", {
  state: () => ({
    token: localStorage.getItem("token") || null,
    role: localStorage.getItem("role") || null,
  }),
  actions: {
    async login(email, password) {
      try {
        const response = await axios.post(
          `${import.meta.env.VITE_BACKEND_URL}/auth/login`,
          {
            email,
            password,
          }
        );

        const token = response.data.token;
        const decoded = jwtDecode(token);

        const role = decoded.role;

        this.token = token;
        this.role = role;

        localStorage.setItem("token", token);
        localStorage.setItem("role", role);

        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

        return true;
      } catch (error) {
        console.error("Login failed:", error);
        throw error;
      }
    },

    logout() {
      this.token = null;
      this.role = null;
      localStorage.removeItem("token");
      localStorage.removeItem("role");
    },
    isAuthenticated() {
      return !!this.token;
    },
  },
});
