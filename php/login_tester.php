<?php
session_start();
require_once __DIR__ . '/conexion_auto.php';

// Buscar el ID del usuario tester
$sql = "SELECT id FROM usuarios WHERE email = 'tester@berrinche.com' LIMIT 1";
$res = $conn->query($sql);
if ($res && $res->num_rows > 0) {
  $user = $res->fetch_assoc();
  $_SESSION['id_usuario'] = $user['id'];
  echo "<script>
    alert('✅ Sesión iniciada como Tester Berrinche');
    window.location.href = '../panel_cliente/panel_cliente.html';
  </script>";
} else {
  echo "⚠️ No se encontró el usuario tester.";
}
?>
