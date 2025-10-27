<?php
require_once("../php/verificar_acceso.php");
verificarRol(['admin']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>📊 Estadísticas | Club del Berrinche</title>

  <!-- Estilos base -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="componentes/estilo_admin.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
      font-family: "Poppins", sans-serif;
      margin: 0;
      background: url("../assets/img/fondo-hexagonos.jpg") no-repeat center center fixed;
      background-size: cover;
      color: #fff;
      overflow-y: auto;
      min-height: 100vh;
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      z-index: 0;
    }

    /* Contenedor principal */
    .contenido {
      position: relative;
      z-index: 1;
      padding: clamp(80px, 8vh, 100px) 20px 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      transition: margin-left .3s ease;
    }

    /* ✅ Cuando el sidebar está activo, desplazamos el contenido */
    .sidebar.active ~ .contenido {
      margin-left: 240px;
    }

    h1.titulo-principal {
      color: #00fff2;
      text-shadow: 0 0 25px #00fff2, 0 0 50px #4b00ff;
      font-size: clamp(1.8rem, 4vw, 2.4rem);
      margin-bottom: 10px;
    }

    .subtitulo {
      color: #ccc;
      margin-bottom: 40px;
      font-size: 1rem;
    }

    /* Canvas del gráfico */
    canvas {
      background: rgba(10, 10, 20, 0.8);
      border-radius: 20px;
      box-shadow: 0 0 40px rgba(0, 255, 242, 0.3);
      padding: 20px;
      max-width: 90%;
    }

    @media(max-width: 768px) {
      canvas { max-width: 100%; }
      .sidebar.active ~ .contenido { margin-left: 0; }
    }
  </style>
</head>

<body>
  <div class="overlay"></div>

  <!-- ✅ Menú lateral actualizado -->
  <?php include __DIR__ . "/componentes/menu_admin.php"; ?>

  <div class="contenido">
    <h1 class="titulo-principal">📊 Estadísticas de Cupones</h1>
    <p class="subtitulo">Visualizá los cupones usados y generados por fecha</p>

    <canvas id="graficoCupones" width="800" height="400"></canvas>
  </div>

  <script>
    const ctx = document.getElementById('graficoCupones');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
        datasets: [{
          label: 'Cupones Usados',
          data: [12, 19, 3, 5, 2, 3, 9],
          borderWidth: 1,
          backgroundColor: 'rgba(0,255,242,0.4)',
          borderColor: '#00fff2'
        }]
      },
      options: {
        scales: { y: { beginAtZero: true } },
        plugins: {
          legend: {
            labels: {
              color: '#00fff2',
              font: { size: 14 }
            }
          }
        }
      }
    });
  </script>
</body>
</html>
