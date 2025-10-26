<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/conexion_auto.php';

// --- Verificar sesión ---
if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(["success" => false, "message" => "Sesión no iniciada"]);
  exit;
}

$id_usuario = $_SESSION['id_usuario'];

// --- Obtener datos del usuario desde la base ---
$sql = "SELECT nombre_apodo, selfie_avatar FROM usuarios WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
  $stmt->close();
  $conn->close();
  exit;
}

$usuario = $res->fetch_assoc();
$stmt->close();
$conn->close();

// --- Validar y normalizar avatar ---
$avatar = trim($usuario['selfie_avatar'] ?? '');
$ruta_base = __DIR__ . '/../'; // base del proyecto

if ($avatar === '' || !file_exists($ruta_base . $avatar)) {
  // Si no tiene selfie o el archivo no existe, usamos el avatar default
  $avatar = 'assets/img/avatars/avatar_default.png';
}

// --- Respuesta final JSON ---
echo json_encode([
  "success" => true,
  "data" => [
    "nombre_apodo" => $usuario['nombre_apodo'] ?? 'Cliente',
    "selfie_avatar" => $avatar // usamos el mismo nombre de clave que espera el menú
  ]
]);
?>
