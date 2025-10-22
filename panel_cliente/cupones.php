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
  <title>Tus Cupones | El Club del Berrinche</title>

  <!-- Estilos globales y del menú -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="componentes/menu_cliente.css">

  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: url("../assets/img/fondo-hexagonos.jpg") no-repeat center center fixed;
      background-size: cover;
      color: #fff;
      height: 100vh;
      overflow-y: auto;
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      backdrop-filter: blur(2px);
      z-index: 0;
    }

    /* === CONTENEDOR PRINCIPAL === */
    .contenedor {
      position: relative;
      z-index: 2;
      background: rgba(10, 10, 20, 0.9);
      border-radius: 25px;
      padding: 25px;
      width: 90%;
      max-width: 700px;
      margin: 80px auto 60px auto;
      box-shadow: 0 0 60px rgba(0,255,242,0.3), 0 0 100px rgba(106,0,255,0.25);
      border: 1px solid rgba(0,255,242,0.25);
      transition: filter 0.3s ease;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 10px;
    }

    h1 {
      color: #00fff2;
      text-shadow: 0 0 25px #00fff2, 0 0 40px #4b00ff;
      margin: 0;
      font-size: 1.6rem;
    }

    .aplicar-codigo {
      display: flex;
      align-items: center;
      gap: 8px;
      justify-content: flex-end;
      flex-wrap: wrap;
    }

    .aplicar-codigo span {
      color: #ccc;
      font-size: 0.9rem;
    }

    .aplicar-codigo input {
      width: 120px;
      padding: 6px 10px;
      border-radius: 20px;
      border: 1px solid #00fff2;
      background: rgba(0,0,0,0.4);
      color: #00fff2;
      outline: none;
      text-transform: uppercase;
      text-align: center;
      font-weight: 600;
      letter-spacing: 1px;
    }

    .aplicar-codigo button {
      background: linear-gradient(90deg, #0037ff, #00fff2);
      border: none;
      border-radius: 20px;
      padding: 6px 14px;
      color: #fff;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
    }

    .aplicar-codigo button:hover {
      transform: scale(1.05);
      box-shadow: 0 0 20px rgba(0,255,242,0.5);
    }

    .cupon {
      background: rgba(255,255,255,0.05);
      border: 1px dashed rgba(0,255,242,0.4);
      border-radius: 15px;
      padding: 15px;
      margin-bottom: 20px;
      text-align: left;
      box-shadow: 0 0 15px rgba(0,255,242,0.2);
      cursor: pointer;
      transition: 0.3s;
    }

    .cupon:hover { transform: scale(1.02); }

    .cupon h3 { margin: 0; color: #00fff2; }
    .cupon p { margin: 5px 0; color: #ccc; font-size: 0.95rem; }

    .qr { display: none; text-align: center; margin-top: 10px; }
    .qr img { width: 120px; height: 120px; }

    .btn-volver {
      background: linear-gradient(90deg, #0037ff, #00fff2);
      border: none;
      color: #fff;
      border-radius: 25px;
      padding: 10px 25px;
      font-weight: 600;
      cursor: pointer;
      display: block;
      margin: 20px auto 0 auto;
      transition: 0.3s;
    }

    .btn-volver:hover {
      transform: scale(1.05);
      box-shadow: 0 0 25px rgba(0,255,242,0.5);
    }
  </style>
</head>

<body>
  <div class="overlay"></div>

  <!-- ✅ Menú lateral desde el componente -->
  <?php include 'componentes/menu_cliente.php'; ?>

  <!-- Contenedor principal -->
  <div class="contenedor" id="contenido">
    <header>
      <h1>🎟️ Tus Cupones</h1>
      <div class="aplicar-codigo">
        <span>Aplicá tu nuevo código:</span>
        <input type="text" id="codigoInput" maxlength="5" placeholder="ABCDE">
        <button id="btnAplicar">Aplicar</button>
      </div>
    </header>

    <div id="listaCupones">
      <p>Cargando cupones...</p>
    </div>

    <button class="btn-volver" onclick="window.location.href='panel_cliente.php'">Volver al Panel</button>
  </div>

  <!-- Scripts -->
  <script src="componentes/menu_cliente.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/qrious/dist/qrious.min.js"></script>

  <script>
    // === CARGAR DATOS DEL USUARIO ===
    fetch("../php/panel_cliente_datos.php")
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const user = data.data;
          document.getElementById("nombreUsuario").textContent = user.nombre_apodo || "Sin nombre";
          document.getElementById("userAvatar").src = user.avatar || "../assets/img/avatars/avatar1.png";
        }
      });

    // === CARGAR CUPONES ===
    async function cargarCupones() {
      const cont = document.getElementById("listaCupones");
      cont.innerHTML = "<p>Cargando cupones...</p>";

      try {
        const res = await fetch("../php/panel_cliente/cupones_listar.php");
        const data = await res.json();

        cont.innerHTML = "";

        if (!data.success || data.cupones.length === 0) {
          cont.innerHTML = "<p>No tenés cupones asignados todavía.</p>";
          return;
        }

        data.cupones.forEach(c => {
          const div = document.createElement("div");
          div.classList.add("cupon");
          div.innerHTML = `
            <h3>${c.nombre}</h3>
            <p>${c.descripcion}</p>
            <p class="fecha">Asignado: ${c.fecha_asignacion}</p>
            <div class="qr">
              <img src="https://api.qrserver.com/v1/create-qr-code/?data=${c.codigo_unico}&size=120x120" alt="QR Cupón">
              <p>${c.codigo_unico}</p>
            </div>
          `;
          div.addEventListener("click", () => {
            const qr = div.querySelector(".qr");
            qr.style.display = qr.style.display === "block" ? "none" : "block";
          });
          cont.appendChild(div);
        });
      } catch (err) {
        console.error(err);
        cont.innerHTML = "<p>Error al cargar los cupones.</p>";
      }
    }

    // === APLICAR NUEVO CÓDIGO ===
    document.getElementById("btnAplicar").addEventListener("click", async () => {
      const codigo = document.getElementById("codigoInput").value.trim().toUpperCase();
      if (codigo.length !== 5) {
        alert("Ingresá un código válido de 5 caracteres.");
        return;
      }
      const res = await fetch("../php/panel_cliente/cupones_aplicar.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ codigo })
      });
      const data = await res.json();
      alert(data.message);
      if (data.success) cargarCupones();
    });

    cargarCupones();
  </script>
</body>
</html>
