<!-- === MENU CLIENTE (COMPONENTE GLOBAL) === -->
<div class="menu-btn" id="menuBtn">
  <div></div><div></div><div></div>
</div>

<div class="sidebar" id="sidebar">
  <!-- Avatar y nombre dinámicos -->
  <img id="userAvatar" class="avatar" src="../assets/img/avatars/avatar1.png" alt="Avatar">
  <div id="nombreUsuario" class="nombre-usuario">Cargando...</div>

  <div class="menu-links">
    <button data-section="inicio">🏠 Inicio</button>
    <button data-section="perfil">👤 Perfil</button>
    <button data-section="cupones">🎟️ Tus Cupones</button>
    <button data-section="menu">🍸 Menú</button>
    <button data-section="dj">🎧 DJ</button>
    <button data-section="karaoke">🎤 Karaoke</button>
    <button data-section="cine">🎬 Silent Cine</button>
    <button data-section="juegos">🎮 Juegos</button>
    <button data-section="vr">🕶️ VR</button>
  </div>

  <button class="logout-btn" id="logoutBtn">🚪 Cerrar sesión</button>
</div>

<script>
  // === CARGAR DATOS DEL USUARIO ===
  fetch("../php/panel_cliente_datos.php")
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
