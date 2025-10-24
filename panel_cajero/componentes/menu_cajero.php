<!-- === MENU CAJERO (COMPONENTE GLOBAL) === -->
<div class="menu-btn" id="menuBtn">
  <div></div><div></div><div></div>
</div>

<div class="sidebar" id="sidebar">
  <!-- Avatar y nombre del cajero -->
  <img id="userAvatar" class="avatar" src="../assets/img/avatars/avatar1.png" alt="Avatar">
  <div id="nombreUsuario" class="nombre-usuario">Cargando...</div>

  <div class="menu-links">
    <button data-section="generar">🎟️ Generar Ticket</button>
    <button data-section="ver">📋 Ver Códigos</button>
  </div>

  <button class="logout-btn" id="logoutBtn">🚪 Cerrar sesión</button>
</div>

<script>
  // === CARGAR DATOS DEL USUARIO ===
  fetch("../php/panel_cajero_datos.php")
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const user = data.data;
        document.getElementById("nombreUsuario").textContent = user.nombre_apodo || "Sin nombre";
        document.getElementById("userAvatar").src = user.avatar || "../assets/img/avatars/avatar1.png";
      } else {
        document.getElementById("nombreUsuario").textContent = "Sin sesión";
      }
    })
    .catch(err => console.error("Error al cargar perfil:", err));
</script>
