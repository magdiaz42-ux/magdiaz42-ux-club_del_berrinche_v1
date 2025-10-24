<?php
session_start();
require_once(__DIR__ . "/conexion_auto.php");

// Verificar que la conexión exista
if (!isset($conn) || $conn->connect_error) {
  die("❌ Error: no se pudo establecer la conexión a la base de datos. " . $conn->connect_error);
}

// Validar campos
if (empty($_POST['usuario']) || empty($_POST['password'])) {
  header("Location: ../login.php?error=campos_vacios");
  exit();
}

$usuario  = trim($_POST['usuario']);
$password = trim($_POST['password']);

// Determinar si es email o teléfono
$campo = filter_var($usuario, FILTER_VALIDATE_EMAIL) ? 'email' : 'telefono';

// Buscar usuario
$stmt = $conn->prepare("
  SELECT id, nombre_apodo, email, telefono, password_hash, rol, activo 
  FROM usuarios 
  WHERE $campo = ?
  LIMIT 1
");
if (!$stmt) {
  die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows === 1) {
  $row = $resultado->fetch_assoc();

  if (password_verify($password, $row['password_hash'])) {
    if ((int)$row['activo'] !== 1) {
      header("Location: ../login.php?error=usuario_inactivo");
      exit();
    }

    // Crear sesión
    $_SESSION['id_usuario'] = $row['id'];
    $_SESSION['nombre']     = $row['nombre_apodo'];
    $_SESSION['rol']        = $row['rol'];
    $_SESSION['email']      = $row['email'];
    $_SESSION['telefono']   = $row['telefono'];

    // Redirigir según rol
    switch ($row['rol']) {
      case 'admin': header("Location: ../panel_admin/panel_admin.php"); break;
      case 'dj': header("Location: ../panel_dj/panel_dj.php"); break;
      case 'cajero': header("Location: ../panel_cajero/panel_cajero.php"); break;
      case 'karaoke': header("Location: ../panel_karaoke/panel_karaoke.php"); break;
      case 'cine': header("Location: ../panel_cine/panel_cine.php"); break;
      case 'juegos': header("Location: ../panel_juegos/panel_juegos.php"); break;
      default: header("Location: ../panel_cliente/panel_cliente.php"); break;
    }
    exit();

  } else {
    header("Location: ../login.php?error=pass_incorrecta");
    exit();
  }
} else {
  header("Location: ../registro/registro_etapa1_datos.html");
  exit();
}

$stmt->close();
$conn->close();
?>
