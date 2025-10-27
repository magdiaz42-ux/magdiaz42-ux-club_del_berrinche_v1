<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['id_usuario']) || empty($_SESSION['rol'])) {
  header("Location: ../login.php?error=sesion_invalida");
  exit;
}

function verificarRol($rolesPermitidos = []) {
  if (!in_array($_SESSION['rol'], $rolesPermitidos)) {
    header("Location: ../login.php?error=acceso_denegado");
    exit;
  }
}
