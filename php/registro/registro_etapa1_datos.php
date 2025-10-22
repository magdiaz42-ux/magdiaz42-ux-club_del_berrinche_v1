<?php
session_start();
require_once __DIR__ . '/../conexion_auto.php';

// --- 1. Recibir datos del formulario ---
$nombre_apodo = trim($_POST['nombre_apodo'] ?? '');
$telefono     = trim($_POST['telefono'] ?? '');
$email        = trim($_POST['email'] ?? '');
$password     = trim($_POST['password'] ?? '');
$codigo_5     = strtoupper(trim($_POST['codigo_5'] ?? ''));

// --- 2. Validaciones básicas ---
if (empty($nombre_apodo) || empty($telefono) || empty($email) || empty($password) || strlen($codigo_5) != 5) {
  http_response_code(400);
  echo "Error: faltan datos o el código no tiene 5 caracteres.";
  exit;
}

// --- 3. Validar que el código exista y esté disponible ---
$sql = "SELECT id, disponible FROM codigos_unicos WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo_5);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  http_response_code(404);
  echo "El código no existe.";
  exit;
}

$codigo = $res->fetch_assoc();
if ($codigo['disponible'] == 0) {
  http_response_code(409);
  echo "El código ya fue utilizado.";
  exit;
}

// --- 4. Validar que el mail no exista ya ---
$check_email = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$check_email->bind_param("s", $email);
$check_email->execute();
$result_email = $check_email->get_result();

if ($result_email->num_rows > 0) {
  http_response_code(409);
  echo "Este email ya está registrado.";
  exit;
}

// --- 5. Cifrar contraseña ---
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// --- 6. Insertar nuevo usuario ---
$insert = $conn->prepare("
  INSERT INTO usuarios (nombre_apodo, telefono, email, password_hash, codigo_5)
  VALUES (?, ?, ?, ?, ?)
");
$insert->bind_param("sssss", $nombre_apodo, $telefono, $email, $password_hash, $codigo_5);

if (!$insert->execute()) {
  http_response_code(500);
  echo "Error al registrar usuario: " . $conn->error;
  exit;
}

$id_usuario = $insert->insert_id;

// --- 7. Marcar código como usado y vincularlo al usuario ---
$update = $conn->prepare("
  UPDATE codigos_unicos
  SET disponible = 0, id_usuario = ?, fecha_asignacion = NOW()
  WHERE codigo = ?
");
$update->bind_param("is", $id_usuario, $codigo_5);
$update->execute();

// --- 8. Crear sesión temporal ---
$_SESSION['id_usuario'] = $id_usuario;
$_SESSION['codigo_5'] = $codigo_5;

// --- 9. Respuesta final ---
echo "OK";
?>
