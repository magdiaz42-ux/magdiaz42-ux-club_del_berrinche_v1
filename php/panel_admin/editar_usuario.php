<?php
require_once("../conexion_auto.php");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['id'])) {
  echo json_encode(["ok" => false, "mensaje" => "Datos incompletos"]);
  exit;
}

$id = (int)$data['id'];
$nombre = trim($data['nombre']);
$email = trim($data['email']);
$rol = trim($data['rol']);
$estado = strtolower(trim($data['estado']));
$telefono = isset($data['telefono']) ? trim($data['telefono']) : '';

$activo = ($estado === 'activo') ? 1 : 0;

$stmt = $conn->prepare("UPDATE usuarios 
  SET nombre_apodo=?, telefono=?, email=?, rol=?, activo=? 
  WHERE id=? AND rol IN ('admin','cajero')");
$stmt->bind_param("ssssii", $nombre, $telefono, $email, $rol, $activo, $id);

if ($stmt->execute()) {
  echo json_encode(["ok" => true, "mensaje" => "Empleado actualizado correctamente."]);
} else {
  echo json_encode(["ok" => false, "mensaje" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
