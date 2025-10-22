<?php
session_start();
require_once __DIR__ . '/../conexion_auto.php';

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(["success" => false, "message" => "No hay sesión activa."]);
  exit;
}

$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT nombre_apodo, telefono, email, selfie_avatar FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo json_encode(["success" => false, "message" => "Usuario no encontrado."]);
  exit;
}

$u = $res->fetch_assoc();
echo json_encode(["success" => true, "data" => $u]);
