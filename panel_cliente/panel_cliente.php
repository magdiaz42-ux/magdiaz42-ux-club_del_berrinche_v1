<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
  header("Location: ../login.html");
  exit;
}
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

  <!-- ✅ Incluimos el menú desde el componente -->
  <?php include '../panel_cliente/componentes/menu_cliente.php'; ?>

  <!-- Contenido principal -->
  <div class="contenido" id="contenido">
    <h1>Bienvenido al Club del Berrinche</h1>
    <p class="mensaje-bienvenida">Elegí una sección desde el menú para comenzar tu experiencia.</p>
  </div>

  <script src="../panel_cliente/componentes/menu_cliente.js" defer></script>
</body>
</html>
