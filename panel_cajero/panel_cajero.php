<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'cajero') {
  header("Location: ../login.php?error=acceso_denegado");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel del Cajero | El Club del Berrinche</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="componentes/menu_cajero.css">

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
      flex-direction: column;
      text-align: center;
    }

    h1 {
      color: #00fff2;
      text-shadow: 0 0 25px #00fff2, 0 0 50px #4b00ff;
      font-size: 2.4rem;
      margin: 0;
    }

    p {
      color: #ccc;
      font-size: 1rem;
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <div class="overlay"></div>

  <!-- Menú lateral -->
  <?php include 'componentes/menu_cajero.php'; ?>

  <!-- Contenido principal -->
  <div class="contenido">
    <h1>El Club del Berrinche</h1>
    <p>Panel del Cajero</p>
  </div>

  <script src="componentes/menu_cajero.js" defer></script>
</body>
</html>
