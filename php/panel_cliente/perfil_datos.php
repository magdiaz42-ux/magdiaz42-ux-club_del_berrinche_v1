<?php
session_start();
require_once __DIR__ . '/../conexion_auto.php';

// --- 1. Verificar sesión ---
if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(["success" => false, "message" => "No hay sesión activa o expiró."]);
  exit;
}

$id_usuario = $_SESSION['id_usuario'];

// --- 2. Buscar datos del usuario ---
$sql = "SELECT nombre_apodo, telefono, email, selfie_avatar FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
  echo json_encode(["success" => false, "message" => "Error preparando la consulta."]);
  exit;
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo json_encode(["success" => false, "message" => "Usuario no encontrado."]);
  exit;
}

$u = $res->fetch_assoc();

// --- 3. Validar y construir ruta de avatar ---
$basePath = "../assets/img/avatars/avatar1.png"; // imagen por defecto

if (!empty($u['selfie_avatar'])) {
  $ruta = trim($u['selfie_avatar']);
  
  // Si viene solo el nombre del archivo (por ej. selfie_123.jpg)
  if (!str_contains($ruta, "uploads/") && !str_contains($ruta, "assets/")) {
    $ruta = "../uploads/" . $ruta;
  }

  // Si el archivo no existe en disco, usar default
  $absolute = realpath(__DIR__ . "/../../" . ltrim($ruta, "./"));
  if (!$absolute || !file_exists($absolute)) {
    $ruta = $basePath;
  }

  $u['selfie_avatar'] = $ruta;
} else {
  $u['selfie_avatar'] = $basePath;
}

// --- 4. Forzar tipos de datos limpios ---
$u['nombre_apodo'] = $u['nombre_apodo'] ?? '';
$u['telefono'] = $u['telefono'] ?? '';
$u['email'] = $u['email'] ?? '';

// --- 5. Respuesta JSON ---
header("Content-Type: application/json; charset=utf-8");
echo json_encode([
  "success" => true,
  "data" => $u
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
