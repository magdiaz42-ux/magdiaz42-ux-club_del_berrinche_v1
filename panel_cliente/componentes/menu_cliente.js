// === MENU CLIENTE (LÓGICA GLOBAL) ===
document.addEventListener("DOMContentLoaded", () => {
  const menuBtn = document.getElementById("menuBtn");
  const sidebar = document.getElementById("sidebar");
  const logoutBtn = document.getElementById("logoutBtn");

  // --- Abrir/cerrar menú ---
  if (menuBtn && sidebar) {
    menuBtn.addEventListener("click", () => {
      menuBtn.classList.toggle("active");
      sidebar.classList.toggle("active");
    });

    // Cerrar al hacer clic fuera del menú
    document.addEventListener("click", (e) => {
      if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
        sidebar.classList.remove("active");
        menuBtn.classList.remove("active");
      }
    });
  }

  // --- Cerrar sesión ---
  if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
      e.preventDefault();
      if (confirm("¿Seguro que querés cerrar sesión?")) {
        fetch("../php/logout.php", { method: "POST" })
          .then(() => (window.location.href = "../login.php?logout=1"))
          .catch((err) => console.error("Error al cerrar sesión:", err));
      }
    });
  }
});
