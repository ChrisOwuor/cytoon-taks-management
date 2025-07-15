// API helper for all requests
let API_BASE_URL = "";

// Load .env file and set API_BASE_URL
fetch("../.env")
  .then((res) => res.text())
  .then((env) => {
    const match = env.match(/API_BASE_URL=(.*)/);
    if (match) API_BASE_URL = match[1].trim();
  });

function getToken() {
  return localStorage.getItem("jwt_token");
}

function apiFetch(path, options = {}) {
  const token = getToken();
  options.headers = options.headers || {};
  options.headers["Content-Type"] = "application/json";
  if (token) options.headers["Authorization"] = "Bearer " + token;
  return fetch(API_BASE_URL + path, options)
    .then((res) => res.json().then((data) => ({ status: res.status, data })))
    .catch((err) => ({ status: 0, data: { error: err.message } }));
}

window.apiFetch = apiFetch;
