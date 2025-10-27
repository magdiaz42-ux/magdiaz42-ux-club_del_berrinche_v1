<?php
require_once("../php/verificar_acceso.php");
verificarRol(['admin']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Panel Administrativo | El Club del Berrinche</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="componentes/estilo_admin.css">
</head>
<body>
  <div class="overlay"></div>
  <?php include __DIR__ . "/componentes/menu_admin.php"; ?>

  <div class="contenido">
    <h1 class="titulo-principal">⚙️ Panel Administrativo</h1>
    <p class="subtitulo">Bienvenido, <strong><?= htmlspecialchars($_SESSION['nombre'] ?? 'Administrador') ?></strong></p>

    <div class="tarjetas-dashboard">
      <div class="card">
        <h3>👥 Usuarios Activos</h3>
        <p id="totalUsuarios">—</p>
      </div>
      <div class="card">
        <h3>💸 Códigos Vendidos Hoy</h3>
        <p id="totalVentas">—</p>
      </div>
      <div class="card">
        <h3>🎟️ Cupones Usados</h3>
        <p id="totalCupones">—</p>
      </div>
    </div>

    <div class="acciones-rapidas">
      <a href="admin_usuarios.php" class="btn">👥 Gestionar Usuarios</a>
      <a href="admin_ventas.php" class="btn">💸 Ver Ventas</a>
      <a href="admin_estadisticas.php" class="btn">📊 Ver Estadísticas</a>
    </div>
  </div>

  <script src="componentes/menu_admin.js" defer></script>
</body>
</html>
