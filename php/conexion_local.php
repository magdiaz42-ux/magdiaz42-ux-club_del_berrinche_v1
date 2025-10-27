<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "club_del_berrinche";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Error de conexión local: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>