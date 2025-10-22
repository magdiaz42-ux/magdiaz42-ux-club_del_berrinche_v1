<?php
session_start();
require_once __DIR__ . '/../conexion_auto.php';

// --- Validar sesión ---
if (!isset($_SESSION['id_usuario'])) {
  http_response_code(403);
  echo "Sesión inválida. Volvé a la etapa 1.";
  exit;
}

$id_usuario = $_SESSION['id_usuario'];

// --- Capturar datos ---
$selfieData = $_POST['selfieData'] ?? null;
$avatarPath = $_POST['avatarSeleccionado'] ?? null;

// --- Validar que haya al menos uno ---
if (!$selfieData && !$avatarPath) {
  http_response_code(400);
  echo "No se recibió imagen ni avatar.";
  exit;
}

$nombreArchivo = null;

// --- Si el usuario subió una selfie (base64) ---
if ($selfieData) {
  // Crear carpeta si no existe
  $carpeta = __DIR__ . '/../../uploads/selfies/';
  if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);

  // Generar nombre único
  $nombreArchivo = 'selfie_' . $id_usuario . '_' . time() . '.png';
  $rutaCompleta = $carpeta . $nombreArchivo;

  // Extraer datos base64 y guardar
  $selfieData = str_replace('data:image/png;base64,', '', $selfieData);
  $selfieData = str_replace(' ', '+', $selfieData);
  $data = base64_decode($selfieData);

  if (!file_put_contents($rutaCompleta, $data)) {
    http_response_code(500);
    echo "Error al guardar la selfie.";
    exit;
  }

  // Ruta relativa para guardar en la base
  $nombreArchivo = 'uploads/selfies/' . $nombreArchivo;
}

// --- Si el usuario eligió avatar ---
if ($avatarPath && !$selfieData) {
  $nombreArchivo = $avatarPath; // ruta directa al avatar
}

// --- Guardar en la base ---
$sql = "UPDATE usuarios SET selfie_avatar = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $nombreArchivo, $id_usuario);

if ($stmt->execute()) {
  echo "OK";
} else {
  http_response_code(500);
  echo "Error al actualizar usuario: " . $conn->error;
}
?>
