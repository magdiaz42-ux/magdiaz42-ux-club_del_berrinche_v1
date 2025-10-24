<?php
session_start();
require_once("../php/conexion_auto.php");

// Verificación de acceso
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'cajero') {
  header("Location: ../login.php?error=acceso_denegado");
  exit;
}

// --- Filtros por fecha (opcional) ---
$filtro_fecha = "";
if (isset($_GET['fecha']) && $_GET['fecha'] != "") {
  $fecha = $_GET['fecha'];
  $filtro_fecha = "WHERE DATE(fecha_generacion) = '$fecha'";
}

$query = "SELECT codigo, disponible, fecha_generacion, fecha_asignacion FROM codigos_unicos $filtro_fecha ORDER BY fecha_generacion DESC";
$resultado = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ver Códigos | Cajero</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="componentes/menu_cajero.css">

  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: url("../assets/img/fondo-hexagonos.jpg") no-repeat center center fixed;
      background-size: cover;
      color: #fff;
      height: 100vh;
      overflow: hidden;
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      backdrop-filter: blur(2px);
      z-index: 0;
    }

    .contenido {
      position: relative;
      z-index: 1;
      padding: 40px;
      text-align: center;
      overflow-y: auto;
      height: 100vh;
    }

    h2 {
      color: #00fff2;
      text-shadow: 0 0 25px #00fff2, 0 0 50px #4b00ff;
      margin-bottom: 20px;
    }

    .filtro {
      margin-bottom: 20px;
    }

    input[type="date"] {
      padding: 8px 15px;
      border-radius: 10px;
      border: 2px solid rgba(0,255,242,0.3);
      background: rgba(255,255,255,0.1);
      color: #fff;
      outline: none;
    }

    input[type="date"]:focus {
      border-color: #00fff2;
      box-shadow: 0 0 10px #00fff2;
    }

    table {
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      border-collapse: collapse;
      background: rgba(10, 10, 20, 0.9);
      border: 1px solid rgba(0,255,242,0.25);
      box-shadow: 0 0 40px rgba(0,255,242,0.2);
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 15px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    th {
      background: rgba(0,255,242,0.15);
      color: #00fff2;
      text-transform: uppercase;
      font-size: 0.9rem;
    }

    tr:hover {
      background: rgba(255,255,255,0.05);
    }

    .disponible {
      color: #00ff88;
      font-weight: 600;
    }

    .usado {
      color: #ff4b4b;
      font-weight: 600;
    }

    .sin-resultados {
      color: #ccc;
      margin-top: 30px;
    }

  </style>
</head>
<body>
  <div class="overlay"></div>
  <?php include 'componentes/menu_cajero.php'; ?>

  <div class="contenido">
    <h2>📋 Códigos generados</h2>

    <form method="GET" class="filtro">
      <label for="fecha">Filtrar por fecha:</label>
      <input type="date" id="fecha" name="fecha" value="<?= isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : '' ?>">
      <button class="btn" type="submit">Filtrar</button>
      <button class="btn" type="button" onclick="window.location.href='ver_codigos.php'">Limpiar</button>
    </form>

    <?php if ($resultado && $resultado->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>Código</th>
            <th>Estado</th>
            <th>Fecha Generación</th>
            <th>Fecha Asignación</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $resultado->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['codigo']) ?></td>
              <td class="<?= $row['disponible'] ? 'disponible' : 'usado' ?>">
                <?= $row['disponible'] ? 'Disponible' : 'Usado' ?>
              </td>
              <td><?= date("d/m/Y H:i", strtotime($row['fecha_generacion'])) ?></td>
              <td><?= $row['fecha_asignacion'] ? date("d/m/Y H:i", strtotime($row['fecha_asignacion'])) : '-' ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="sin-resultados">No hay códigos generados para esta fecha.</p>
    <?php endif; ?>
  </div>

  <script src="componentes/menu_cajero.js" defer></script>
</body>
</html>
