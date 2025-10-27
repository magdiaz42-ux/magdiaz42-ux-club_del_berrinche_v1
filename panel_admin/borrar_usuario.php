<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

require_once("../conexion_auto.php");
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  echo json_encode(["ok" => false, "mensaje" => "🚫 Acceso denegado"]);
  exit;
}

if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
  echo json_encode(["ok" => false, "mensaje" => "ID de usuario inválido"]);
  exit;
}

$id = intval($_GET['id']);

// 🔥 Eliminar físicamente (empleados)
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  echo json_encode(["ok" => true, "mensaje" => "🗑️ Usuario eliminado correctamente."]);
} else {
  echo json_encode(["ok" => false, "mensaje" => "❌ Error al eliminar: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
