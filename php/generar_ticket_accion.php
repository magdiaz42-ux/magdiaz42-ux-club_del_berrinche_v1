<?php
session_start();
require_once(__DIR__ . "/conexion_auto.php");
require_once(__DIR__ . "/lib/phpqrcode.php");

header("Content-Type: application/json; charset=utf-8");

// ⚙️ DEBUG: mostrar errores del servidor
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Validar sesión
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'cajero') {
  echo json_encode(["success" => false, "message" => "Acceso denegado"]);
  exit;
}

// ✅ Generar código único
$codigo = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 5);
$fecha = date("Y-m-d H:i:s");
$cajero = $_SESSION['id_usuario'];

// ✅ Insertar en la base
$stmt = $conn->prepare("INSERT INTO codigos_unicos (codigo, disponible, fecha_generacion, id_usuario) VALUES (?, 1, ?, ?)");
if (!$stmt) {
  echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conn->error]);
  exit;
}
$stmt->bind_param("ssi", $codigo, $fecha, $cajero);
if (!$stmt->execute()) {
  echo json_encode(["success" => false, "message" => "Error al insertar: " . $stmt->error]);
  exit;
}
$stmt->close();

// ✅ Generar carpeta para QR si no existe
$qrDir = __DIR__ . "/../assets/qr_tickets/";
if (!file_exists($qrDir)) {
  mkdir($qrDir, 0777, true);
}

// ✅ Crear QR con dirección al index
$url = "http://127.0.0.1/club_del_berrinche_v1/index.html?code=" . urlencode($codigo);
$qrFile = $qrDir . "qr_" . $codigo . ".png";
QRcode::png($url, $qrFile, QR_ECLEVEL_L, 6, 2);

// ✅ Verificamos que el archivo se generó
if (!file_exists($qrFile)) {
  echo json_encode(["success" => false, "message" => "No se pudo generar el QR."]);
  exit;
}

// ✅ Enviamos respuesta JSON
echo json_encode([
  "success" => true,
  "codigo" => $codigo,
  "qr" => "../assets/qr_tickets/qr_" . $codigo . ".png",
  "url" => $url
]);

$conn->close();
?>
