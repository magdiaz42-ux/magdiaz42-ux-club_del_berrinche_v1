<?php
session_start();
require_once("../php/conexion_auto.php");

$result = $conn->query("SELECT * FROM codigos_unicos ORDER BY fecha_generado DESC LIMIT 50");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Listado de códigos | Cajero</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background:black;color:white;font-family:Poppins,sans-serif;text-align:center">
<h1>Códigos generados</h1>
<table border="1" cellspacing="0" cellpadding="8" align="center" style="color:white;border-color:#00fff2;">
<tr><th>Código</th><th>Estado</th><th>Fecha</th><th>Generado por</th></tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($row['codigo']); ?></td>
  <td><?= htmlspecialchars($row['estado']); ?></td>
  <td><?= htmlspecialchars($row['fecha_generado']); ?></td>
  <td><?= htmlspecialchars($row['generado_por']); ?></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
