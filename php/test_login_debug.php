<?php
ini_set('display_errors',1); error_reporting(E_ALL);
require_once(__DIR__ . '/conexion_auto.php');

$email = 'admin@clubberrinche.com';
$pass  = 'admin123';

$stmt = $conn->prepare("SELECT id, email, activo, rol, password_hash FROM usuarios WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows === 1) {
  $u = $res->fetch_assoc();
  echo "len_hash: " . strlen($u['password_hash']) . "<br>";
  echo "hash: " . htmlspecialchars($u['password_hash']) . "<br>";
  echo "activo: " . $u['activo'] . " rol: " . $u['rol'] . "<br>";
  echo "verify: " . (password_verify($pass, $u['password_hash']) ? "OK" : "FAIL");
} else {
  echo "No existe el usuario.";
}
