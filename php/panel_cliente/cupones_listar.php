<?php
session_start();
require_once __DIR__ . '/../conexion_auto.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(["success" => false, "message" => "No hay sesión activa."]);
  exit;
}

$id_usuario = $_SESSION['id_usuario'];

$sql = "
SELECT 
  cb.nombre,
  cb.caracteristicas,
  cb.tipo,
  ca.codigo_unico,
  ca.fecha_asignacion,
  ca.fecha_vencimiento
FROM cupones_asignados ca
JOIN cupones_base cb ON ca.id_cupon_base = cb.id
WHERE ca.id_usuario = ?
ORDER BY ca.fecha_asignacion DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  echo json_encode(["success" => false, "message" => "Error en la consulta: " . $conn->error]);
  exit;
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

$cupones = [];
while ($row = $res->fetch_assoc()) {
  $cupones[] = [
    "nombre" => $row['nombre'],
    "caracteristicas" => $row['caracteristicas'],
    "tipo" => $row['tipo'],
    "codigo_unico" => $row['codigo_unico'],
    "fecha_asignacion" => $row['fecha_asignacion'],
    "fecha_vencimiento" => $row['fecha_vencimiento'] ?? null
  ];
}

echo json_encode([
  "success" => true,
  "cupones" => $cupones
], JSON_UNESCAPED_UNICODE);
