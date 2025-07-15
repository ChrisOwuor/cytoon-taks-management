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

function loadUserTasks() {
  window.apiFetch("/api/tasks/user-tasks").then((res) => {
    if (res.status === 200) {
      renderUserTasks(res.data);
    } else {
      showToast("Failed to load tasks", true);
    }
  });
}

function renderUserTasks(tasks) {
  const list = document.getElementById("userTasksList");
  list.innerHTML = tasks
    .map(
      (task) => `
    <div class="task-item">
      <span>${task.title} (${task.status})</span>
      <button onclick="viewTask(${task.id})">View</button>
      <button onclick="updateTaskStatus(${task.id})">Update Status</button>
    </div>
  `
    )
    .join("");
}

window.viewTask = function (id) {
  window.apiFetch(`/api/tasks/${id}`).then((res) => {
    if (res.status === 200) {
      const task = res.data;
      showModal({
        title: "Task Details",
        content: `
          <div><strong>Title:</strong> ${task.title}</div>
          <div><strong>Description:</strong> ${task.description}</div>
          <div><strong>Deadline:</strong> ${
            task.deadline ? task.deadline.split("T")[0] : ""
          }</div>
          <div><strong>Status:</strong> ${task.status}</div>
        `,
      });
    }
  });
};

window.updateTaskStatus = function (id) {
  showModal({
    title: "Update Task Status",
    content: `
      <form id="statusForm">
        <select id="newStatus">
          <option value="pending">Pending</option>
          <option value="in_progress">In Progress</option>
          <option value="completed">Completed</option>
        </select>
        <button type="submit">Update</button>
      </form>
    `,
    onOpen: () => {
      document.getElementById("statusForm").onsubmit = function (e) {
        e.preventDefault();
        const status = document.getElementById("newStatus").value;
        window
          .apiFetch(`/api/tasks/status-update/${id}`, {
            method: "PUT",
            body: JSON.stringify({ status }),
          })
          .then((res) => {
            if (res.status === 200) {
              showToast("Status updated");
              closeModal();
              loadUserTasks();
            } else {
              showToast(res.data.message || "Error", true);
            }
          });
      };
    },
  });
};

// Initial load
loadUserTasks();
