<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// ✅ Ruta universal
$conexionPath = dirname(__DIR__, 2) . '/php/conexion_auto.php';
if (file_exists($conexionPath)) {
  require_once $conexionPath;
}

// ==============================
// Datos del cajero
// ==============================
$id_usuario = $_SESSION['id_usuario'] ?? null;
$nombre_usuario = "Cajero";
$avatar_usuario = "../assets/img/avatars/avatar_default.png";

if ($id_usuario && isset($conn)) {
  $stmt = $conn->prepare("SELECT nombre_apodo, selfie_avatar FROM usuarios WHERE id = ?");
  $stmt->bind_param("i", $id_usuario);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($res && $res->num_rows > 0) {
    $user = $res->fetch_assoc();
    $nombre_usuario = $user['nombre_apodo'] ?: "Cajero";
    if (!empty($user['selfie_avatar'])) {
      $avatar_usuario = "../" . ltrim($user['selfie_avatar'], '/');
    }
  }
  $stmt->close();
}
?>

<!-- === BOTÓN HAMBURGUESA === -->
<div id="menuBtn" class="menu-btn">
  <div></div><div></div><div></div>
</div>

<!-- === PANEL LATERAL === -->
<div id="sidebar" class="sidebar">
  <div class="sidebar-content">
    <img src="<?php echo $avatar_usuario; ?>" class="avatar" alt="Avatar">
    <div class="nombre-usuario"><?php echo htmlspecialchars($nombre_usuario); ?></div>

    <div class="menu-links">
      <button onclick="window.location.href='generar_ticket.php'">🎟️ Generar Ticket</button>
      <button onclick="window.location.href='ver_codigos.php'">📋 Ver Tickets</button>
    </div>

    <button class="logout-btn" onclick="window.location.href='../php/logout.php'">🚪 Cerrar sesión</button>
  </div>
</div>

<!-- ✅ Carga forzada del JS -->
<script>
console.log("📦 Cargando menú cajero...");
const s = document.createElement("script");
s.src = "../panel_cajero/componentes/menu_cajero.js";
s.defer = true;
document.body.appendChild(s);
</script>
