<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (!isset($_SESSION['id_usuario'])) {
  header("Location: ../login.php");
  exit;
}
?>
<div class="menu-btn" id="menuBtn">
  <div></div><div></div><div></div>
</div>

<div class="sidebar" id="sidebar">
  <!-- Avatar y nombre dinámicos -->
  <img id="userAvatar" class="avatar" src="../assets/img/avatars/avatar_default.png" alt="Avatar">
  <div id="nombreUsuario" class="nombre-usuario">Cargando...</div>

  <div class="menu-links">
    <button onclick="window.location.href='../panel_cliente/panel_cliente.php'">🏠 Inicio</button>
    <button onclick="window.location.href='../panel_cliente/perfil.php'">👤 Perfil</button>
    <button onclick="window.location.href='../panel_cliente/cupones.php'">🎟️ Tus Cupones</button>
    <button onclick="window.location.href='../panel_cliente/menu.php'">🍸 Menú</button>
    <button onclick="window.location.href='../panel_cliente/dj.php'">🎧 DJ</button>
    <button onclick="window.location.href='../panel_cliente/karaoke.php'">🎤 Karaoke</button>
    <button onclick="window.location.href='../panel_cliente/cine.php'">🎬 Silent Cine</button>
    <button onclick="window.location.href='../panel_cliente/juegos.php'">🎮 Juegos</button>
    <button onclick="window.location.href='../panel_cliente/vr.php'">🕶️ VR</button>
  </div>

  <!-- 🚪 Cerrar sesión -->
  <form action="../php/logout.php" method="POST" style="width:100%;margin-top:auto;">
    <button type="submit" class="logout-btn">🚪 Cerrar sesión</button>
  </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  fetch("../php/panel_cliente_datos.php")
    .then(res => res.json())
    .then(data => {
      if (!data.success || !data.data) {
        document.getElementById("nombreUsuario").textContent = "Sin sesión";
        return;
      }

      const user = data.data;
      const nombre = user.nombre_apodo || "Cliente";
      let avatarSrc = "../assets/img/avatars/avatar_default.png";

      // ✅ Manejar tanto selfies (base64) como rutas guardadas
      if (user.selfie_avatar && user.selfie_avatar.trim() !== "") {
        const ruta = user.selfie_avatar.trim();
        if (ruta.startsWith("data:image")) {
          // 📸 Imagen base64 (selfie tomada desde cámara)
          avatarSrc = ruta;
        } else if (ruta.startsWith("../") || ruta.startsWith("assets/") || ruta.startsWith("php/")) {
          // 📂 Ruta ya relativa al proyecto
          avatarSrc = ruta;
        } else {
          // 🔄 Si viene sin prefijo, lo corregimos
          avatarSrc = "../" + ruta.replace(/^\/+/, "");
        }
      }

      const nombreElem = document.getElementById("nombreUsuario");
      const avatarElem = document.getElementById("userAvatar");

      if (nombreElem) nombreElem.textContent = nombre;
      if (avatarElem) avatarElem.src = avatarSrc;
    })
    .catch(err => console.error("Error al cargar datos del usuario:", err));
});
</script>
