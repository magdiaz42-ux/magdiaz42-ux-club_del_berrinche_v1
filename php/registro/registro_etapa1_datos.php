<?php
session_start();
require_once __DIR__ . '/../conexion_auto.php';

// --- 1. Recibir datos del formulario ---
$nombre_apodo   = trim($_POST['nombre_apodo'] ?? '');
$telefono       = trim($_POST['telefono'] ?? '');
$email          = trim($_POST['email'] ?? '');
$password       = trim($_POST['password'] ?? '');
$codigo_entrada = strtoupper(trim($_POST['codigo_entrada'] ?? ''));

// --- 2. Validaciones ---
if (empty($nombre_apodo) || empty($telefono) || empty($password) || strlen($codigo_entrada) != 5) {
  http_response_code(400);
  echo "Faltan datos o el código no tiene 5 caracteres.";
  exit;
}

// --- 3. Validar código ---
$sql = "SELECT id, disponible FROM codigos_unicos WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo_entrada);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo "El código no existe.";
  exit;
}

$codigo = $res->fetch_assoc();
if ((int)$codigo['disponible'] !== 1) {
  echo "El código ya fue utilizado o no está disponible.";
  exit;
}

$id_codigo = $codigo['id'];

// --- 4. Buscar o crear usuario ---
if (!empty($email)) {
  $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ? OR telefono = ?");
  $check->bind_param("ss", $email, $telefono);
} else {
  $check = $conn->prepare("SELECT id FROM usuarios WHERE telefono = ?");
  $check->bind_param("s", $telefono);
}

$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
  $id_usuario = $result->fetch_assoc()['id'];
} else {
  $password_hash = password_hash($password, PASSWORD_DEFAULT);

  // ✅ Si el email viene vacío, lo seteamos a NULL
  $email = trim($email) === "" ? null : $email;

  $insert = $conn->prepare("
    INSERT INTO usuarios (nombre_apodo, telefono, email, password_hash, fecha_registro)
    VALUES (?, ?, ?, ?, NOW())
  ");
  $insert->bind_param("ssss", $nombre_apodo, $telefono, $email, $password_hash);

  if (!$insert->execute()) {
    echo "Error al registrar usuario: " . $conn->error;
    exit;
  }

  $id_usuario = $insert->insert_id;
}

// --- 5. Marcar código como usado ---
$update = $conn->prepare("
  UPDATE codigos_unicos
  SET disponible = 0, id_usuario = ?, fecha_asignacion = NOW()
  WHERE id = ?
");
$update->bind_param("ii", $id_usuario, $id_codigo);
$update->execute();

// --- 6. Asignar cupones ---
$fecha_vencimiento = date('Y-m-d H:i:s', strtotime('+30 days'));

// Cupón fijo
$sql_fijo = "SELECT id FROM cupones_base WHERE tipo='fijo' AND activo=1 LIMIT 1";
$fijo = $conn->query($sql_fijo);
if ($fijo && $fijo->num_rows > 0) {
  $id_cupon_base_fijo = $fijo->fetch_assoc()['id'];
  $codigo_unico_cupon = 'CUP-' . $id_usuario . '-' . strtoupper(substr(md5(uniqid()), 0, 5));
  $conn->query("
    INSERT INTO cupones_asignados (id_usuario, id_codigo, id_cupon_base, codigo_unico, fecha_vencimiento)
    VALUES ($id_usuario, $id_codigo, $id_cupon_base_fijo, '$codigo_unico_cupon', '$fecha_vencimiento')
  ");
}

// Cupones aleatorios
$sql_rand = "SELECT id FROM cupones_base WHERE tipo='random' AND activo=1 ORDER BY RAND() LIMIT 9";
$result_rand = $conn->query($sql_rand);
while ($row = $result_rand->fetch_assoc()) {
  $id_cupon_base = $row['id'];
  $codigo_unico_cupon = 'CUP-' . $id_usuario . '-' . strtoupper(substr(md5(uniqid()), 0, 5));
  $conn->query("
    INSERT INTO cupones_asignados (id_usuario, id_codigo, id_cupon_base, codigo_unico, fecha_vencimiento)
    VALUES ($id_usuario, $id_codigo, $id_cupon_base, '$codigo_unico_cupon', '$fecha_vencimiento')
  ");
}

// --- 7. Sesión y salida ---
$_SESSION['id_usuario'] = $id_usuario;
$_SESSION['codigo_entrada'] = $codigo_entrada;

echo "OK";
?>
