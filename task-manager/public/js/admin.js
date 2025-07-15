importScriptsIfNeeded();

function importScriptsIfNeeded() {
  if (!window.apiFetch) {
    const script = document.createElement("script");
    script.src = "js/api.js";
    document.head.appendChild(script);
  }
}

document.getElementById("logoutBtn").onclick = function () {
  localStorage.clear();
  window.location.href = "index.html";
};

function loadUsers() {
  window.apiFetch("/api/users").then((res) => {
    if (res.status === 200) {
      renderUsers(res.data);
    } else {
      showToast("Failed to load users", true);
    }
  });
}

function renderUsers(users) {
  const list = document.getElementById("usersList");
  list.innerHTML = users
    .map(
      (user) => `
    <div class="user-item">
      <span>${user.name} (${user.email})</span>
      <button onclick="editUser(${user.id})">Edit</button>
      <button onclick="deleteUser(${user.id})">Delete</button>
    </div>
  `
    )
    .join("");
}

window.editUser = function (id) {
  // Show modal for editing user
  showUserModal(id);
};

window.deleteUser = function (id) {
  if (confirm("Delete this user?")) {
    window.apiFetch(`/api/users/${id}`, { method: "DELETE" }).then((res) => {
      if (res.status === 200) {
        showToast("User deleted");
        loadUsers();
      } else {
        showToast("Delete failed", true);
      }
    });
  }
};

document.getElementById("addUserBtn").onclick = function () {
  showUserModal();
};

function showUserModal(id) {
  // Fetch user if editing, else show empty form
  let user = { name: "", email: "", password: "" };
  if (id) {
    window.apiFetch(`/api/users/${id}`).then((res) => {
      if (res.status === 200) {
        user = res.data;
        openUserModal(user, id);
      }
    });
  } else {
    openUserModal(user);
  }
}

function openUserModal(user, id) {
  showModal({
    title: id ? "Edit User" : "Add User",
    content: `
      <form id="userForm">
        <input type="text" id="userName" value="${
          user.name || ""
        }" placeholder="Name" required>
        <input type="email" id="userEmail" value="${
          user.email || ""
        }" placeholder="Email" required>
        <input type="password" id="userPassword" placeholder="Password" ${
          id ? "" : "required"
        }>
        <button type="submit">${id ? "Update" : "Create"}</button>
      </form>
    `,
    onOpen: () => {
      document.getElementById("userForm").onsubmit = function (e) {
        e.preventDefault();
        const name = document.getElementById("userName").value;
        const email = document.getElementById("userEmail").value;
        const password = document.getElementById("userPassword").value;
        const payload = { name, email };
        if (password) payload.password = password;
        const method = id ? "PUT" : "POST";
        const url = id ? `/api/users/${id}` : "/api/users";
        window
          .apiFetch(url, {
            method,
            body: JSON.stringify(payload),
          })
          .then((res) => {
            if (res.status === 200) {
              showToast(id ? "User updated" : "User created");
              closeModal();
              loadUsers();
            } else {
              showToast(res.data.message || "Error", true);
            }
          });
      };
    },
  });
}

// Tasks
function loadTasks() {
  window.apiFetch("/api/tasks").then((res) => {
    if (res.status === 200) {
      renderTasks(res.data);
    } else {
      showToast("Failed to load tasks", true);
    }
  });
}

function renderTasks(tasks) {
  const list = document.getElementById("tasksList");
  list.innerHTML = tasks
    .map(
      (task) => `
    <div class="task-item">
      <span>${task.title} (Assigned to: ${
        task.user?.name || "Unassigned"
      })</span>
      <button onclick="editTask(${task.id})">Edit</button>
    </div>
  `
    )
    .join("");
}

window.editTask = function (id) {
  showTaskModal(id);
};

document.getElementById("addTaskBtn").onclick = function () {
  showTaskModal();
};

function showTaskModal(id) {
  let task = {
    title: "",
    description: "",
    deadline: "",
    status: "",
    user_id: "",
  };
  if (id) {
    window.apiFetch(`/api/tasks/${id}`).then((res) => {
      if (res.status === 200) {
        task = res.data;
        openTaskModal(task, id);
      }
    });
  } else {
    openTaskModal(task);
  }
}

function openTaskModal(task, id) {
  // Fetch users for assignment
  window.apiFetch("/api/users").then((res) => {
    const users = res.data || [];
    showModal({
      title: id ? "Edit Task" : "Add Task",
      content: `
        <form id="taskForm">
          <input type="text" id="taskTitle" value="${
            task.title || ""
          }" placeholder="Title" required>
          <textarea id="taskDesc" placeholder="Description">${
            task.description || ""
          }</textarea>
          <input type="date" id="taskDeadline" value="${
            task.deadline ? task.deadline.split("T")[0] : ""
          }" required>
          <select id="taskStatus">
            <option value="pending" ${
              task.status === "pending" ? "selected" : ""
            }>Pending</option>
            <option value="in_progress" ${
              task.status === "in_progress" ? "selected" : ""
            }>In Progress</option>
            <option value="completed" ${
              task.status === "completed" ? "selected" : ""
            }>Completed</option>
          </select>
          <select id="taskUser">
            <option value="">Unassigned</option>
            ${users
              .map(
                (u) =>
                  `<option value="${u.id}" ${
                    task.user_id == u.id ? "selected" : ""
                  }>${u.name}</option>`
              )
              .join("")}
          </select>
          <button type="submit">${id ? "Update" : "Create"}</button>
        </form>
      `,
      onOpen: () => {
        document.getElementById("taskForm").onsubmit = function (e) {
          e.preventDefault();
          const payload = {
            title: document.getElementById("taskTitle").value,
            description: document.getElementById("taskDesc").value,
            deadline: document.getElementById("taskDeadline").value,
            status: document.getElementById("taskStatus").value,
            user_id: document.getElementById("taskUser").value || null,
          };
          const method = id ? "PUT" : "POST";
          const url = id ? `/api/tasks/${id}` : "/api/tasks/add";
          window
            .apiFetch(url, {
              method,
              body: JSON.stringify(payload),
            })
            .then((res) => {
              if (res.status === 200) {
                showToast(id ? "Task updated" : "Task created");
                closeModal();
                loadTasks();
              } else {
                showToast(res.data.message || "Error", true);
              }
            });
        };
      },
    });
  });
}

// Initial load
loadUsers();
loadTasks();
