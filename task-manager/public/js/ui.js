// Modal helper
function showModal({ title, content, onOpen }) {
  const modalContainer = document.getElementById("modalContainer");
  modalContainer.innerHTML = `
    <div class="modal">
      <h3>${title}</h3>
      <div>${content}</div>
      <button onclick="closeModal()">Close</button>
    </div>
  `;
  modalContainer.style.display = "flex";
  if (onOpen) onOpen();
}

function closeModal() {
  const modalContainer = document.getElementById("modalContainer");
  modalContainer.style.display = "none";
  modalContainer.innerHTML = "";
}

window.showModal = showModal;
window.closeModal = closeModal;

// Toast helper
function showToast(message, isError = false) {
  let toast = document.createElement("div");
  toast.className = "toast";
  toast.style.background = isError ? "#d9534f" : "#333";
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.remove();
  }, 2500);
}
window.showToast = showToast;
