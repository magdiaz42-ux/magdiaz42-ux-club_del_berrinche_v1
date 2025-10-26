<?php
session_start();
require_once __DIR__ . '/../conexion_auto.php';
header('Content-Type: application/json; charset=utf-8');

// Mostrar errores (solo para debug)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = [
  "success" => false,
  "message" => "Error desconocido",
  "debug" => []
];

// --- Validar sesión ---
if (!isset($_SESSION['id_usuario'])) {
  $response["message"] = "Sesión no activa.";
  echo json_encode($response);
  exit;
}

$id_usuario = $_SESSION['id_usuario'];
$input = json_decode(file_get_contents("php://input"), true);
$codigo = strtoupper(trim($input['codigo'] ?? ''));

if (strlen($codigo) !== 5) {
  $response["message"] = "Código inválido (debe tener 5 caracteres).";
  echo json_encode($response);
  exit;
}

// --- Buscar el código ---
$sql = "SELECT id, disponible, id_usuario FROM codigos_unicos WHERE codigo = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
  $response["message"] = "Error preparando SELECT de código";
  $response["debug"][] = $conn->error;
  echo json_encode($response);
  exit;
}

$stmt->bind_param("s", $codigo);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  $response["message"] = "El código no existe.";
  echo json_encode($response);
  exit;
}

$codigo_data = $res->fetch_assoc();
$id_codigo = $codigo_data['id'];
$disponible = (int)$codigo_data['disponible'];
$id_usuario_asignado = $codigo_data['id_usuario'];

if ($disponible === 0 && $id_usuario_asignado !== $id_usuario) {
  $response["message"] = "Este código ya fue usado por otro cliente.";
  echo json_encode($response);
  exit;
}
if ($disponible === 0 && $id_usuario_asignado === $id_usuario) {
  $response["message"] = "Ya aplicaste este código antes.";
  echo json_encode($response);
  exit;
}

// --- Buscar cupones ---
$cupones = [];
$random = $conn->query("SELECT id FROM cupones_base WHERE activo = 1 AND tipo = 'random' ORDER BY RAND() LIMIT 9");
if (!$random) {
  $response["message"] = "Error obteniendo cupones random.";
  $response["debug"][] = $conn->error;
  echo json_encode($response);
  exit;
}
while ($c = $random->fetch_assoc()) $cupones[] = $c['id'];

$fijo = $conn->query("SELECT id FROM cupones_base WHERE tipo = 'fijo' AND activo = 1 LIMIT 1");
if ($fijo && $fijo->num_rows > 0) {
  $f = $fijo->fetch_assoc();
  array_unshift($cupones, $f['id']);
}

// --- Insertar ---
$insertados = 0;
foreach ($cupones as $id_cupon_base) {
  $codigo_unico = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 10);
  $sqlInsert = "INSERT INTO cupones_asignados
                (id_usuario, id_codigo, id_cupon_base, codigo_unico, fecha_asignacion, fecha_vencimiento, canjeado)
                VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY), 0)";
  $stmt = $conn->prepare($sqlInsert);
  if (!$stmt) {
    $response["debug"][] = "Error preparando INSERT: " . $conn->error;
    continue;
  }
  $stmt->bind_param("iiis", $id_usuario, $id_codigo, $id_cupon_base, $codigo_unico);
  if ($stmt->execute()) {
    $insertados++;
  } else {
    $response["debug"][] = "Error ejecutando INSERT: " . $stmt->error;
  }
}

// --- Actualizar código ---
$sqlUpdate = "UPDATE codigos_unicos 
              SET disponible = 0, fecha_asignacion = NOW(), id_usuario = ? 
              WHERE id = ?";
$stmt = $conn->prepare($sqlUpdate);
if (!$stmt) {
  $response["debug"][] = "Error preparando UPDATE: " . $conn->error;
} else {
  $stmt->bind_param("ii", $id_usuario, $id_codigo);
  $stmt->execute();
}

// --- Respuesta final ---
$response["success"] = $insertados > 0;
$response["message"] = $insertados > 0 
  ? "🎉 ¡Se asignaron $insertados cupones nuevos (válidos por 24hs)!"
  : "❌ No se asignaron cupones.";
$response["debug"][] = "Insertados: $insertados";

echo json_encode($response, JSON_PRETTY_PRINT);
$conn->close();
?>
