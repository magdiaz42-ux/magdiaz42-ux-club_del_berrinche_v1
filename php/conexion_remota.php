<?php
$servername = "sqlXXX.infinityfree.com"; // <-- tu host real
$username   = "if0_XXXXXXX";             // <-- tu usuario
$password   = "TU_PASSWORD";             // <-- tu pass
$database   = "if0_XXXXXXX_berrinche";   // <-- tu DB

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Error de conexión remota: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
