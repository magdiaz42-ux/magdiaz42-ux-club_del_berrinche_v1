<?php
// ====================================
// PANEL PRINCIPAL DEL CAJERO
// ====================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// ✅ Verificación de acceso
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'cajero') {
  header("Location: ../login.php?error=acceso_denegado");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Panel del Cajero | El Club del Berrinche</title>

  <!-- Fuentes y estilos -->
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
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Fondo oscuro translúcido */
    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(4px);
      z-index: 0;
    }

    /* Contenido principal */
    .contenido {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 20px;
      height: 100vh;
    }

    h1 {
      color: #00fff2;
      text-shadow: 0 0 25px #00fff2, 0 0 50px #4b00ff;
      font-size: clamp(2rem, 4vw, 2.8rem);
      margin-bottom: 12px;
      animation: glow 2s ease-in-out infinite alternate;
    }

    p {
      color: #bbb;
      font-size: clamp(1rem, 2vw, 1.2rem);
      letter-spacing: 1px;
    }

    @keyframes glow {
      from { text-shadow: 0 0 20px #00fff2, 0 0 40px #4b00ff; }
      to { text-shadow: 0 0 40px #00fff2, 0 0 80px #4b00ff; }
    }

    @media (max-width: 600px) {
      .contenido { padding: 80px 10px; }
      h1 { font-size: 1.8rem; }
      p { font-size: 1rem; }
    }
  </style>
</head>

<body>
  <div class="overlay"></div>

  <!-- ✅ Incluimos el menú con ruta absoluta -->
  <?php include __DIR__ . '/componentes/menu_cajero.php'; ?>

  <!-- Contenido principal -->
  <div class="contenido">
    <h1>🎟️ El Club del Berrinche</h1>
    <p>Panel del Cajero</p>
  </div>

  <!-- Script del menú -->
  <script src="componentes/menu_cajero.js" defer></script>
</body>
</html>
