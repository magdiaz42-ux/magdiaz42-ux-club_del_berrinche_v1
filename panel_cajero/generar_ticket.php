<?php
// ============================
// PANEL CAJERO - GENERAR TICKET
// ============================

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// 🚫 Verificación de acceso
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
  <title>Generar Ticket | Panel Cajero</title>

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
      height: 100vh;
      overflow: hidden;
    }

    /* 🔧 Overlay al fondo (no tapa el menú) */
    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      backdrop-filter: blur(3px);
      z-index: 0;
    }

    .contenedor-principal {
      position: relative;
      z-index: 1;
      height: 100vh;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 20px;
    }

    .panel-box {
      background: rgba(0, 0, 0, 0.65);
      border: 1px solid rgba(0,255,242,0.3);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 0 25px rgba(0,255,242,0.3);
      max-width: 480px;
      width: 90%;
    }

    h2 {
      color: #00fff2;
      text-shadow: 0 0 25px #00fff2, 0 0 50px #4b00ff;
      margin-bottom: 20px;
    }

    .btn {
      background: linear-gradient(90deg, #0037ff, #00fff2);
      border: none;
      color: #fff;
      font-weight: 600;
      padding: 14px 28px;
      border-radius: 25px;
      cursor: pointer;
      font-size: 1rem;
      margin: 10px 5px;
      box-shadow: 0 0 20px rgba(0,255,242,0.4);
      transition: all 0.3s ease;
    }

    .btn:hover {
      transform: scale(1.05);
      box-shadow: 0 0 30px rgba(0,255,242,0.6);
    }

    .resultado {
      display: none;
      margin-top: 25px;
      animation: fadeIn 0.5s ease;
    }

    .qr-box {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      margin-top: 10px;
    }

    .qr-box img {
      width: 180px;
      height: 180px;
      border: 2px solid #00fff2;
      border-radius: 10px;
      box-shadow: 0 0 25px rgba(0,255,242,0.4);
    }

    .codigo {
      font-size: 1.5rem;
      color: #00fff2;
      margin-top: 10px;
      font-weight: bold;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* ✅ RESPONSIVE */
    @media (max-width: 600px) {
      .panel-box { padding: 20px; width: 95%; }
      h2 { font-size: 1.5rem; }
      .btn { font-size: 0.9rem; padding: 12px 20px; }
      .qr-box img { width: 150px; height: 150px; }
      .codigo { font-size: 1.2rem; }
    }
  </style>
</head>

<body>
  <div class="overlay"></div>

  <!-- ✅ Menú lateral -->
  <?php include 'componentes/menu_cajero.php'; ?>

  <!-- ✅ Contenedor principal -->
  <div class="contenedor-principal">
    <div class="panel-box">
      <h2>🎟️ Generar Ticket Único</h2>

      <button id="btnGenerar" class="btn">Generar Ticket</button>
      <button id="btnImprimir" class="btn" style="display:none;" onclick="window.print()">🖨️ Imprimir</button>

      <div class="resultado" id="resultado">
        <div class="qr-box">
          <img id="qrImg" src="" alt="QR del ticket">
          <div class="codigo" id="codigoTexto"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Script generar ticket -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const btnGenerar = document.getElementById("btnGenerar");
      const btnImprimir = document.getElementById("btnImprimir");
      const resultado = document.getElementById("resultado");
      const qrImg = document.getElementById("qrImg");
      const codigoTexto = document.getElementById("codigoTexto");

      btnGenerar.addEventListener("click", async () => {
        btnGenerar.disabled = true;
        btnGenerar.innerText = "Generando...";

        try {
          const res = await fetch("../php/generar_ticket_accion.php");
          const data = await res.json();

          if (data.success) {
            resultado.style.display = "block";
            qrImg.src = data.qr;
            codigoTexto.textContent = "Código: " + data.codigo;
            btnImprimir.style.display = "inline-block";
          } else {
            alert("❌ Error: " + data.message);
          }
        } catch (err) {
          alert("⚠️ Error de conexión con el servidor.");
        } finally {
          btnGenerar.disabled = false;
          btnGenerar.innerText = "Generar Ticket";
        }
      });
    });
  </script>

  <!-- ✅ Script del menú (ruta absoluta garantizada) -->
  <script src="/club_del_berrinche_v1/panel_cajero/componentes/menu_cajero.js"></script>
</body>
</html>
