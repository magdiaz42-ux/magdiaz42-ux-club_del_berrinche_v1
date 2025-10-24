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
  <title>Generar Ticket | El Club del Berrinche</title>

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
      padding-top: 100px;
      text-align: center;
    }

    h1 {
      color: #00fff2;
      text-shadow: 0 0 25px #00fff2, 0 0 50px #4b00ff;
      margin-bottom: 40px;
    }

    .btn {
      background: linear-gradient(90deg, #0037ff, #00fff2);
      border: none;
      color: #fff;
      font-weight: 600;
      padding: 14px 25px;
      border-radius: 25px;
      box-shadow: 0 0 25px rgba(0, 255, 242, 0.6);
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      font-size: 1rem;
      text-transform: uppercase;
    }

    .btn:hover {
      transform: scale(1.05);
      box-shadow: 0 0 40px rgba(0, 255, 242, 0.8);
    }

    #resultado {
      margin-top: 40px;
    }

    #resultado img {
      margin-top: 15px;
      width: 200px;
      border: 2px solid rgba(0,255,242,0.3);
      border-radius: 10px;
      box-shadow: 0 0 25px rgba(0,255,242,0.4);
    }
  </style>
</head>
<body>
  <div class="overlay"></div>
  <?php include 'componentes/menu_cajero.php'; ?>

  <div class="contenido">
    <h1>🎟️ Generar nuevo ticket</h1>
    <button class="btn" id="btnGenerar">Generar Ticket</button>

    <div id="resultado"></div>
  </div>

  <script src="componentes/menu_cajero.js" defer></script>

  <script>
    document.getElementById("btnGenerar").addEventListener("click", () => {
      const btn = document.getElementById("btnGenerar");
      btn.disabled = true;
      btn.textContent = "Generando...";

      fetch("generar_ticket_accion.php")
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            document.getElementById("resultado").innerHTML = `
              <h2>Código: ${data.codigo}</h2>
              <img src="${data.qr}" alt="QR del ticket">
              <br><br>
              <button class="btn" onclick="window.print()">🖨️ Imprimir Ticket</button>
            `;
          } else {
            document.getElementById("resultado").innerHTML = `<p style="color:#ff6b6b;">❌ ${data.message}</p>`;
          }
        })
        .catch(() => {
          document.getElementById("resultado").innerHTML = `<p style="color:#ff6b6b;">⚠️ Error al generar el ticket.</p>`;
        })
        .finally(() => {
          btn.disabled = false;
          btn.textContent = "Generar Ticket";
        });
    });
  </script>
</body>
</html>
