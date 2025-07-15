document
  .getElementById("loginForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault();
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const alertDiv = document.getElementById("loginAlert");
    alertDiv.textContent = "";

    const res = await window.apiFetch("/api/auth/login", {
      method: "POST",
      body: JSON.stringify({ email, password }),
    });

    if (res.status === 200 && res.data.token) {
      localStorage.setItem("jwt_token", res.data.token);
      localStorage.setItem("user_role", res.data.role);
      if (res.data.role === "admin") {
        window.location.href = "admin.html";
      } else {
        window.location.href = "user.html";
      }
    } else {
      alertDiv.textContent = res.data.message || "Login failed.";
      alertDiv.style.color = "red";
    }
  });

// Redirect if already logged in
const token = localStorage.getItem("jwt_token");
const role = localStorage.getItem("user_role");
if (token && role) {
  window.location.href = role === "admin" ? "admin.html" : "user.html";
}
