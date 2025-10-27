<?php
require_once("../conexion_auto.php");
header('Content-Type: application/json; charset=utf-8');

session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  echo json_encode([]);
  exit;
}

$where = "WHERE 1=1";
$params = [];
$types  = "";

// Filtros opcionales
if (!empty($_GET['rol'])) {
  $where .= " AND rol = ?";
  $params[] = $_GET['rol'];
  $types .= "s";
}

if (!empty($_GET['estado'])) {
  if ($_GET['estado'] === 'activo') {
    $where .= " AND activo = 1";
  } elseif ($_GET['estado'] === 'inactivo') {
    $where .= " AND activo = 0";
  }
}

$sql = "SELECT id, nombre_apodo, email, rol, activo, selfie_avatar 
        FROM usuarios
        $where
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$usuarios = [];
while ($row = $result->fetch_assoc()) {
  $usuarios[] = [
    "id" => $row['id'],
    "nombre_apodo" => $row['nombre_apodo'],
    "email" => $row['email'],
    "rol" => $row['rol'],
    "estado" => $row['activo'] ? 'Activo' : 'Inactivo',
    "avatar" => $row['selfie_avatar']
  ];
}

echo json_encode($usuarios);

$stmt->close();
$conn->close();
?>
