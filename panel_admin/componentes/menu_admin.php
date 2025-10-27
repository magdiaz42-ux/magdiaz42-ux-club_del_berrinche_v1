<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!-- Botón de menú -->
<div class="menu-btn" id="menuBtn">
  <div></div><div></div><div></div>
</div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
  <img src="../assets/img/avatars/avatar_default.png" class="avatar" alt="Avatar">
  <div class="nombre-usuario">
    <?= htmlspecialchars($_SESSION['nombre'] ?? 'Administrador') ?>
  </div>

  <ul class="menu-opciones">
    <li><a href="admin_dashboard.php">🏠 Inicio</a></li>
    <li><a href="admin_usuarios.php">👥 Usuarios</a></li>
    <li><a href="admin_ventas.php">💸 Ventas</a></li>
    <li><a href="admin_estadisticas.php">📊 Estadísticas</a></li>
    <li><a href="../php/logout.php">🚪 Cerrar sesión</a></li>
  </ul>
</nav>

<style>
/* ====== Ajustes visuales y z-index ====== */
.menu-btn {
  position: fixed;
  top: 20px;
  left: 20px;
  z-index: 1100; /* 🔥 arriba de todo */
  cursor: pointer;
}
.menu-btn div {
  width: 30px;
  height: 3px;
  background: #00fff2;
  margin: 6px 0;
  transition: 0.3s;
}

.sidebar {
  position: fixed;
  top: 0;
  left: -240px;
  width: 240px;
  height: 100vh;
  background: rgba(10, 10, 25, 0.95);
  backdrop-filter: blur(10px);
  transition: left 0.3s ease;
  z-index: 1050; /* 🔥 sobre contenido */
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 60px;
  box-shadow: 0 0 30px rgba(0,255,242,0.3);
}
.sidebar.active {
  left: 0;
}

.sidebar .avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  margin-bottom: 10px;
  border: 2px solid #00fff2;
}
.nombre-usuario {
  font-weight: 600;
  margin-bottom: 20px;
  color: #00fff2;
}
.menu-opciones {
  list-style: none;
  padding: 0;
  width: 100%;
}
.menu-opciones li a {
  display: block;
  text-decoration: none;
  color: #fff;
  padding: 12px 20px;
  border-left: 3px solid transparent;
  transition: 0.2s;
}
.menu-opciones li a:hover {
  background: rgba(0,255,242,0.1);
  border-left: 3px solid #00fff2;
}
</style>

<script>
const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");

// Alternar visibilidad del sidebar
menuBtn.addEventListener("click", () => {
  sidebar.classList.toggle("active");
});
</script>
