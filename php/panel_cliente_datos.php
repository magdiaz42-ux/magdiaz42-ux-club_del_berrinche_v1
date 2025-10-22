<?php
session_start();
require_once __DIR__ . '/conexion_auto.php';

// --- Validar sesión ---
if (!isset($_SESSION['id_usuario'])) {
  http_response_code(401);
  echo json_encode(["success" => false, "message" => "Sesión no iniciada."]);
  exit;
}

$id_usuario = $_SESSION['id_usuario'];

// --- Obtener datos del usuario ---
$sql = "SELECT nombre_apodo, email, telefono, selfie_avatar 
        FROM usuarios 
        WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  http_response_code(404);
  echo json_encode(["success" => false, "message" => "Usuario no encontrado."]);
  exit;
}

$usuario = $res->fetch_assoc();

echo json_encode([
  "success" => true,
  "data" => [
    "nombre_apodo" => $usuario['nombre_apodo'],
    "email" => $usuario['email'],
    "telefono" => $usuario['telefono'],
    "avatar" => $usuario['selfie_avatar']
  ]
]);
?>
