<?php
session_start();
require_once __DIR__ . '/../conexion_auto.php';

// --- Verificar sesión ---
if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(["success" => false, "message" => "Sesión no iniciada."]);
  exit;
}

$id_usuario = $_SESSION['id_usuario'];

// --- Leer datos recibidos ---
$nombre_apodo = $_POST['nombre'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$email = $_POST['email'] ?? '';
$selfie_avatar = $_POST['selfie_avatar'] ?? ''; // base64 o URL

// --- Validar campos ---
if (empty($nombre_apodo) || empty($telefono) || empty($email)) {
  echo json_encode(["success" => false, "message" => "Faltan datos obligatorios."]);
  exit;
}

// --- Si se envía una imagen base64, guardarla en el servidor ---
if (strpos($selfie_avatar, 'data:image') === 0) {
  $imgData = explode(',', $selfie_avatar);
  $imgBase64 = base64_decode($imgData[1]);

  // Crear carpeta si no existe
  $carpeta = __DIR__ . '/../../assets/img/usuarios/';
  if (!is_dir($carpeta)) {
    mkdir($carpeta, 0777, true);
  }

  // Nombre de archivo único
  $nombreArchivo = 'user_' . $id_usuario . '_' . time() . '.png';
  $rutaArchivo = $carpeta . $nombreArchivo;
  file_put_contents($rutaArchivo, $imgBase64);

  // Guardar ruta relativa para BD
  $selfie_avatar = '../assets/img/usuarios/' . $nombreArchivo;
}

// --- Actualizar datos ---
$sql = "UPDATE usuarios 
        SET nombre_apodo = ?, telefono = ?, email = ?, selfie_avatar = ? 
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $nombre_apodo, $telefono, $email, $selfie_avatar, $id_usuario);

if ($stmt->execute()) {
  echo json_encode(["success" => true, "message" => "Perfil actualizado correctamente."]);
} else {
  echo json_encode(["success" => false, "message" => "Error al actualizar el perfil."]);
}
?>
