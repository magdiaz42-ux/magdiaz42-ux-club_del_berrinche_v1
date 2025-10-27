<?php
require_once("../conexion_auto.php");
header('Content-Type: application/json; charset=utf-8');

session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  echo json_encode(["ok" => false, "mensaje" => "🚫 Acceso denegado"]);
  exit;
}

// Validar campos
$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['nombre_apodo']) || empty($data['email']) || empty($data['password']) || empty($data['rol'])) {
  echo json_encode(["ok" => false, "mensaje" => "Faltan campos obligatorios"]);
  exit;
}

$nombre_apodo = trim($data['nombre_apodo']);
$email = trim($data['email']);
$rol = trim($data['rol']);
$password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
$selfie_avatar = $data['selfie_avatar'] ?? 'avatar_default.png';

$stmt = $conn->prepare("
  INSERT INTO usuarios (nombre_apodo, email, password_hash, selfie_avatar, rol, activo, fecha_registro, registro_completo)
  VALUES (?, ?, ?, ?, ?, 1, NOW(), 1)
");
$stmt->bind_param("sssss", $nombre_apodo, $email, $password_hash, $selfie_avatar, $rol);

if ($stmt->execute()) {
  echo json_encode(["ok" => true, "mensaje" => "✅ Usuario creado correctamente."]);
} else {
  echo json_encode(["ok" => false, "mensaje" => "❌ Error al crear usuario: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
