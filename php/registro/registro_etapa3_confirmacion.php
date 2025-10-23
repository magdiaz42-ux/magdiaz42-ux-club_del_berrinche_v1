<?php
session_start();
require_once __DIR__ . '/../conexion_auto.php';

// --- Validar sesión ---
if (!isset($_SESSION['id_usuario'])) {
  http_response_code(403);
  echo json_encode(["success" => false, "message" => "Sesión inválida. Volvé a la etapa 1."]);
  exit;
}

$id_usuario = $_SESSION['id_usuario'];

// --- Recibir y decodificar JSON ---
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
  echo json_encode(["success" => false, "message" => "Datos inválidos o vacíos."]);
  exit;
}

// --- Capturar respuestas (hasta 5 preguntas) ---
$respuestasQuiz = $data['respuestasQuiz'] ?? [];

$r1 = $respuestasQuiz[0]['respuesta'] ?? null;
$r2 = $respuestasQuiz[1]['respuesta'] ?? null;
$r3 = $respuestasQuiz[2]['respuesta'] ?? null;
$r4 = $respuestasQuiz[3]['respuesta'] ?? null;
$r5 = $respuestasQuiz[4]['respuesta'] ?? null;

// --- Verificar si existen las columnas respuesta4 y respuesta5 ---
$columnCheck = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'respuesta4'");
if ($columnCheck->num_rows === 0) {
  $conn->query("ALTER TABLE usuarios ADD COLUMN respuesta4 VARCHAR(255) NULL AFTER respuesta3");
}
$columnCheck = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'respuesta5'");
if ($columnCheck->num_rows === 0) {
  $conn->query("ALTER TABLE usuarios ADD COLUMN respuesta5 VARCHAR(255) NULL AFTER respuesta4");
}

// --- Guardar respuestas y marcar registro completo ---
$update = $conn->prepare("
  UPDATE usuarios 
  SET respuesta1 = ?, respuesta2 = ?, respuesta3 = ?, respuesta4 = ?, respuesta5 = ?, registro_completo = 1
  WHERE id = ?
");
$update->bind_param("sssssi", $r1, $r2, $r3, $r4, $r5, $id_usuario);

if (!$update->execute()) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Error al guardar las respuestas."]);
  exit;
}

// --- Mantener la sesión activa ---
$_SESSION['id_usuario'] = $id_usuario;

// --- Respuesta final ---
echo json_encode([
  "success" => true,
  "message" => "Registro completo. ¡Gracias por unirte al Club del Berrinche!"
]);
?>
