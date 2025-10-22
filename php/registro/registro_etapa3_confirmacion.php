<?php
session_start();
require_once __DIR__ . '/../conexion_auto.php';

// --- Recibir y decodificar JSON ---
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
  echo json_encode(["success" => false, "message" => "Datos inválidos o vacíos."]);
  exit;
}

$nombre = $data['nombre'] ?? '';
$codigoPais = $data['codigoPais'] ?? '';
$telefono = $data['telefono'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$codigo_entrada = strtoupper($data['codigo_entrada'] ?? '');
$usuarioAvatar = $data['usuarioAvatar'] ?? '';
$respuestasQuiz = $data['respuestasQuiz'] ?? [];

// --- Validaciones básicas ---
if (empty($nombre) || empty($telefono) || empty($codigo_entrada) || empty($password)) {
  echo json_encode(["success" => false, "message" => "Faltan datos obligatorios."]);
  exit;
}

// --- Buscar usuario por código ---
$sql_user = "SELECT id FROM usuarios WHERE codigo_5 = ? LIMIT 1";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $codigo_entrada);
$stmt_user->execute();
$res_user = $stmt_user->get_result();

if ($res_user->num_rows === 0) {
  echo json_encode(["success" => false, "message" => "Código no válido o usuario no encontrado."]);
  exit;
}

$user = $res_user->fetch_assoc();
$id_usuario = $user['id'];

// --- Guardar respuestas y marcar registro como completo ---
$r1 = $respuestasQuiz[0]['respuesta'] ?? null;
$r2 = $respuestasQuiz[1]['respuesta'] ?? null;
$r3 = $respuestasQuiz[2]['respuesta'] ?? null;
$r4 = $respuestasQuiz[3]['respuesta'] ?? null;
$r5 = $respuestasQuiz[4]['respuesta'] ?? null;

$update = $conn->prepare("
  UPDATE usuarios 
  SET respuesta1=?, respuesta2=?, respuesta3=?, respuesta4=?, respuesta5=?, registro_completo=1
  WHERE id=?
");
$update->bind_param("sssssi", $r1, $r2, $r3, $r4, $r5, $id_usuario);
$update->execute();

// --- Buscar ID del código único ---
$sql_code = "SELECT id FROM codigos_unicos WHERE codigo = ? LIMIT 1";
$stmt_code = $conn->prepare($sql_code);
$stmt_code->bind_param("s", $codigo_entrada);
$stmt_code->execute();
$res_code = $stmt_code->get_result();

if ($res_code->num_rows === 0) {
  echo json_encode(["success" => false, "message" => "No se encontró el código asociado."]);
  exit;
}

$code = $res_code->fetch_assoc();
$id_codigo = $code['id'];

// ---------------------------
// 🧾 Generar los 10 cupones
// ---------------------------

// 1 cupón fijo
$sql_fijo = "SELECT id FROM cupones_base WHERE tipo='fijo' AND activo=1 LIMIT 1";
$res_fijo = $conn->query($sql_fijo);
$id_fijo = $res_fijo && $res_fijo->num_rows > 0 ? $res_fijo->fetch_assoc()['id'] : null;

// 9 cupones aleatorios
$sql_random = "SELECT id FROM cupones_base WHERE tipo='random' AND activo=1 ORDER BY RAND() LIMIT 9";
$res_random = $conn->query($sql_random);
$ids_random = [];
while ($row = $res_random->fetch_assoc()) {
  $ids_random[] = $row['id'];
}

// --- Insertar los cupones asignados ---
$insert = $conn->prepare("
  INSERT INTO cupones_asignados (id_usuario, id_codigo, id_cupon_base, codigo_unico, fecha_asignacion)
  VALUES (?, ?, ?, ?, NOW())
");

$all_cupons = [];
if ($id_fijo) $all_cupons[] = $id_fijo;
$all_cupons = array_merge($all_cupons, $ids_random);

foreach ($all_cupons as $id_cupon) {
  $codigo_unico_cupon = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
  $insert->bind_param("iiis", $id_usuario, $id_codigo, $id_cupon, $codigo_unico_cupon);
  $insert->execute();
}


// --- Cerrar sesión del registro ---
session_destroy();

// --- Respuesta final ---
echo json_encode([
  "success" => true,
  "message" => "Registro completo. Se asignaron los cupones al usuario."
]);
?>
