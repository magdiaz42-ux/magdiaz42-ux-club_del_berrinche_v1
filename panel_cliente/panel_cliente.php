<?php
session_start();
require_once("../php/conexion_auto.php");

// 🚫 Verificación de acceso
if (!isset($_SESSION['id_usuario'])) {
  header("Location: ../login.php");
  exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_apodo = "";

// ✅ Obtener el nombre/apodo del cliente desde la BD
$sql = "SELECT nombre_apodo FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows > 0) {
  $user = $resultado->fetch_assoc();
  $nombre_apodo = htmlspecialchars($user['nombre_apodo']);
} else {
  $nombre_apodo = "Cliente";
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel del Cliente | El Club del Berrinche</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../panel_cliente/componentes/menu_cliente.css">

  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: url("../assets/img/fondo-hexagonos.jpg") no-repeat center center fixed;
      background-size: cover;
      color: #fff;
      height: 100vh;
      overflow: hidden;
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      backdrop-filter: blur(2px);
      z-index: 0;
    }

    .contenido {
      position: relative;
      z-index: 1;
      height: 100vh;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      flex-direction: column;
      transition: filter 0.3s ease;
    }

    .sidebar.active ~ .contenido {
      filter: blur(2px) brightness(0.7);
    }

    h1 {
      color: #00fff2;
      text-shadow: 0 0 25px #00fff2, 0 0 50px #4b00ff;
      font-size: 2.4rem;
      margin-bottom: 10px;
    }

    .mensaje-bienvenida {
      color: #ccc;
      font-size: 1rem;
      max-width: 500px;
    }
  </style>
</head>

<body>
  <div class="overlay"></div>

  <!-- ✅ Menú lateral -->
  <?php include '../panel_cliente/componentes/menu_cliente.php'; ?>

  <!-- Contenido principal -->
  <div class="contenido" id="contenido">
    <h1>¡Bienvenido, <?php echo $nombre_apodo; ?>! 👋</h1>
    <p class="mensaje-bienvenida">
      Elegí una sección desde el menú para comenzar tu experiencia en <strong>El Club del Berrinche</strong>.
    </p>
  </div>

  <script src="../panel_cliente/componentes/menu_cliente.js" defer></script>
</body>
</html>
