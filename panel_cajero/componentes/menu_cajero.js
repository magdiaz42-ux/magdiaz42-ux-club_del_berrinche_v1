document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ menu_cajero.js ejecutado");

  const menuBtn = document.getElementById("menuBtn");
  const sidebar = document.getElementById("sidebar");

  if (!menuBtn || !sidebar) {
    console.error("❌ No se encontró el botón o el sidebar");
    return;
  }

  menuBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    console.log("🎯 Click en menú hamburguesa");
    menuBtn.classList.toggle("active");
    sidebar.classList.toggle("active");
  });

  document.addEventListener("click", (e) => {
    if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
      sidebar.classList.remove("active");
      menuBtn.classList.remove("active");
    }
  });
});
