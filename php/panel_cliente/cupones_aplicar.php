<?php
session_start();
require_once __DIR__ . '/../../conexion_auto.php';
header('Content-Type: application/json');

// --- Validar sesión ---
if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(["success" => false, "message" => "Sesión no activa."]);
  exit;
}

$id_usuario = $_SESSION['id_usuario'];
$input = json_decode(file_get_contents("php://input"), true);
$codigo = strtoupper(trim($input['codigo'] ?? ''));

// --- Validar formato ---
if (strlen($codigo) !== 5) {
  echo json_encode(["success" => false, "message" => "Código inválido (debe tener 5 caracteres)."]);
  exit;
}

/* 🔹 Verificar si el código existe y está activo */
$sql = "SELECT id FROM usuarios WHERE codigo_5 = ? AND activo = 1 LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo json_encode(["success" => false, "message" => "Código no válido o inactivo."]);
  exit;
}

$codigo_data = $res->fetch_assoc();
$id_codigo = $codigo_data['id'];

/* 🔹 Verificar si el código ya fue aplicado por CUALQUIER usuario */
$sqlCheck = "SELECT COUNT(*) AS total FROM cupones_asignados WHERE id_codigo = ?";
$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("i", $id_codigo);
$stmt->execute();
$resCheck = $stmt->get_result()->fetch_assoc();

if ($resCheck['total'] > 0) {
  echo json_encode(["success" => false, "message" => "Este código ya fue usado por otro cliente."]);
  exit;
}

/* 🔹 Traer 9 cupones aleatorios activos */
$cupones_base = $conn->query("SELECT id FROM cupones_base WHERE activo = 1 ORDER BY RAND() LIMIT 9");
$cupones = [];
while ($c = $cupones_base->fetch_assoc()) $cupones[] = $c['id'];

/* 🔹 Agregar el cupón fijo (tipo = 'fijo') */
$cupon_fijo = $conn->query("SELECT id FROM cupones_base WHERE tipo = 'fijo' AND activo = 1 LIMIT 1");
if ($cupon_fijo && $cupon_fijo->num_rows > 0) {
  $fijo = $cupon_fijo->fetch_assoc();
  array_unshift($cupones, $fijo['id']);
}

/* 🔹 Insertar los 10 cupones */
$insertados = 0;
foreach ($cupones as $id_cupon_base) {
  $codigo_unico = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 10);
  $sqlInsert = "INSERT INTO cupones_asignados 
                (id_usuario, id_codigo, id_cupon_base, codigo_unico, fecha_asignacion, fecha_vencimiento, canjeado)
                VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY), 0)";
  $stmt = $conn->prepare($sqlInsert);
  $stmt->bind_param("iiis", $id_usuario, $id_codigo, $id_cupon_base, $codigo_unico);
  if ($stmt->execute()) $insertados++;
}

echo json_encode([
  "success" => true,
  "message" => "🎉 ¡Se asignaron $insertados cupones nuevos a tu cuenta!"
]);
?>
