<?php
session_start();
require_once("../php/conexion_auto.php");

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
  header("Location: ../login.php?error=acceso_denegado");
  exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- FILTROS DE FECHAS ---
$where = "WHERE 1=1";
$params = [];
$types  = "";

if (!empty($_GET['fecha_desde'])) {
  $where .= " AND fecha_generacion >= ?";
  $params[] = $_GET['fecha_desde'].' '.(!empty($_GET['hora_desde'])?$_GET['hora_desde']:'00:00:00');
  $types   .= "s";
}
if (!empty($_GET['fecha_hasta'])) {
  $where .= " AND fecha_generacion <= ?";
  $params[] = $_GET['fecha_hasta'].' '.(!empty($_GET['hora_hasta'])?$_GET['hora_hasta']:'23:59:59');
  $types   .= "s";
}

$sql = "SELECT codigo, disponible, fecha_generacion, fecha_asignacion
        FROM codigos_unicos
        $where
        ORDER BY fecha_generacion DESC";

$resultado = false;
$stmt = $conn->prepare($sql);

if ($stmt) {
  if ($params) { $stmt->bind_param($types, ...$params); }
  if ($stmt->execute()) {
    $resultado = $stmt->get_result();
  } else {
    echo "<div style='color:red;text-align:center;margin-top:30px;'>❌ Error al ejecutar consulta: {$stmt->error}</div>";
  }
} else {
  echo "<div style='color:red;text-align:center;margin-top:30px;'>❌ Error al preparar consulta: {$conn->error}</div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>💸 Ventas | Panel Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

<style>
/* ====== MISMO ESTILO QUE ver_codigos.php ====== */
body {
  font-family:"Poppins",sans-serif;
  margin:0;
  background:url("../assets/img/fondo-hexagonos.jpg") no-repeat center/cover fixed;
  color:#fff;
  overflow-y:auto;
  min-height:100dvh;
}

/* Fondo oscuro translúcido */
.overlay{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.75);
  z-index:0;
}

/* Contenedor principal del contenido */
.contenido{
  position:relative;
  z-index:1;
  padding:clamp(80px,8vh,100px) 20px 40px;
  display:flex;
  flex-direction:column;
  align-items:center;
  transition:margin-left .3s ease;
}

/* ✅ Cuando el sidebar está activo, el contenido se desplaza (no se superpone) */
.sidebar.active ~ .contenido{
  margin-left:240px;
}

/* Títulos */
h2{
  color:#00fff2;
  text-shadow:0 0 25px #00fff2,0 0 50px #4b00ff;
  font-size:clamp(1.6rem,4vw,2rem);
  margin-bottom:18px;
  text-align:center;
}

/* Filtros */
.filtro{
  display:flex;
  flex-wrap:wrap;
  justify-content:center;
  gap:10px;
  margin-bottom:20px;
}
input[type="date"],input[type="time"]{
  padding:8px 12px;
  border-radius:10px;
  border:2px solid rgba(0,255,242,0.3);
  background:rgba(255,255,255,0.1);
  color:#fff;
}
.btn{
  background:linear-gradient(90deg,#0037ff,#00fff2);
  border:none;
  color:#fff;
  font-weight:600;
  padding:10px 18px;
  border-radius:22px;
  box-shadow:0 0 25px rgba(0,255,242,.5);
  transition:.2s transform,.2s box-shadow;
  cursor:pointer;
}
.btn:hover{
  transform:scale(1.03);
  box-shadow:0 0 35px rgba(0,255,242,.7);
}

/* Tabla */
table{
  width:100%;
  max-width:900px;
  border-collapse:collapse;
  background:rgba(10,10,20,0.9);
  border-radius:10px;
  overflow:hidden;
  box-shadow:0 0 30px rgba(0,255,242,0.2);
}
th,td{
  padding:10px 8px;
  border-bottom:1px solid rgba(255,255,255,0.1);
}
th{
  background:rgba(0,255,242,0.15);
  color:#00fff2;
  text-transform:uppercase;
  font-size:0.85rem;
}
.disponible{color:#00ff88;font-weight:600;}
.usado{color:#ff4b4b;font-weight:600;}
@media(max-width:600px){
  table{font-size:0.8rem;}
  .sidebar.active ~ .contenido{margin-left:0;} /* en móvil el contenido no se corre */
}
</style>
</head>
<body>
<div class="overlay"></div>

<?php include __DIR__ . "/componentes/menu_admin.php"; ?>

<div class="contenido">
  <h2>💸 Ventas / Códigos Generados</h2>

  <form method="GET" class="filtro">
    <div>
      <label>Desde:</label>
      <input type="date" name="fecha_desde" value="<?= htmlspecialchars($_GET['fecha_desde'] ?? '') ?>">
      <input type="time" name="hora_desde" value="<?= htmlspecialchars($_GET['hora_desde'] ?? '') ?>">
    </div>
    <div>
      <label>Hasta:</label>
      <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($_GET['fecha_hasta'] ?? '') ?>">
      <input type="time" name="hora_hasta" value="<?= htmlspecialchars($_GET['hora_hasta'] ?? '') ?>">
    </div>
    <button class="btn" type="submit">Filtrar</button>
    <button class="btn" type="button" onclick="window.location.href='admin_ventas.php'">Limpiar</button>
  </form>

  <?php if ($resultado && $resultado->num_rows > 0): ?>
  <table>
    <thead><tr><th>Código</th><th>Estado</th><th>Generado</th><th>Asignado</th></tr></thead>
    <tbody>
      <?php while ($row = $resultado->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['codigo']) ?></td>
        <td class="<?= (int)$row['disponible'] ? 'disponible':'usado' ?>">
          <?= (int)$row['disponible'] ? 'Disponible':'Usado' ?>
        </td>
        <td><?= date("d/m/Y H:i", strtotime($row['fecha_generacion'])) ?></td>
        <td><?= $row['fecha_asignacion'] ? date("d/m/Y H:i", strtotime($row['fecha_asignacion'])) : '-' ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p>No hay códigos en el rango indicado.</p>
  <?php endif; ?>
</div>

</body>
</html>
<?php
if (!empty($stmt) && $stmt instanceof mysqli_stmt) { @mysqli_stmt_close($stmt); }
if (!empty($conn) && $conn instanceof mysqli) { $conn->close(); }
?>
