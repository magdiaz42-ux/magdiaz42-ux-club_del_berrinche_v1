// === MENU CAJERO (LÓGICA GLOBAL) ===
const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");

menuBtn.addEventListener("click", () => {
  menuBtn.classList.toggle("active");
  sidebar.classList.toggle("active");
});

// === Navegación ===
document.querySelectorAll(".menu-links button").forEach(btn => {
  btn.addEventListener("click", () => {
    const section = btn.getAttribute("data-section");
    switch (section) {
      case "generar":
        window.location.href = "generar_ticket.php";
        break;
      case "ver":
        window.location.href = "ver_codigos.php";
        break;
      default:
        alert(`Sección "${section}" en desarrollo 🧩`);
    }
    sidebar.classList.remove("active");
    menuBtn.classList.remove("active");
  });
});

// === Cerrar sesión ===
const logoutBtn = document.getElementById("logoutBtn");
if (logoutBtn) {
  logoutBtn.addEventListener("click", () => {
    if (confirm("¿Seguro que querés cerrar sesión?")) {
      fetch("../php/logout.php").then(() => (window.location.href = "../login.php"));
    }
  });
}
