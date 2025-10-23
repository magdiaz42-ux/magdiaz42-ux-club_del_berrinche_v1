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

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="componentes/menu_cliente.css">

  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: url("../assets/img/fondo-hexagonos.jpg") no-repeat center center fixed;
      background-size: cover;
      color: #fff;
      overflow-y: auto;
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      backdrop-filter: blur(2px);
      z-index: 0;
    }

    .contenedor {
      position: relative;
      z-index: 2;
      background: rgba(10, 10, 20, 0.9);
      border-radius: 25px;
      padding: 25px;
      width: 90%;
      max-width: 1000px;
      margin: 100px auto;
      box-shadow: 0 0 60px rgba(0,255,242,0.3), 0 0 100px rgba(106,0,255,0.25);
      border: 1px solid rgba(0,255,242,0.25);
      transition: margin-left 0.3s ease, filter 0.3s ease;
    }

    .sidebar.active ~ .contenedor {
      margin-left: 290px;
      filter: brightness(0.9);
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 25px;
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

    /* === GRILLA === */
    .grid-cupones {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
    }

    /* === CUPONES CURVADOS CON EFECTO 3D === */
    .cupon {
      position: relative;
      padding: 18px 22px;
      border-radius: 18px;
      text-align: left;
      cursor: pointer;
      color: #fff;
      box-shadow: 0 6px 12px rgba(0,0,0,0.4), inset 0 -4px 10px rgba(0,0,0,0.25);
      border: 2px solid rgba(255,255,255,0.2);
      background: linear-gradient(145deg, #555, #333);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .cupon:hover {
      transform: scale(1.05);
      box-shadow: 0 0 25px rgba(0,255,242,0.5);
    }

    /* === COLORES POR TIPO === */
    .cupon.tipo-fijo {
      background: linear-gradient(145deg, #ff4d6d, #c9184a);
    }

    .cupon.tipo-random {
      background: linear-gradient(145deg, #ffd166, #ffb703);
    }

    .cupon.tipo-extra {
      background: linear-gradient(145deg, #06d6a0, #118ab2);
    }

    /* Tipografía con efecto suave 3D */
    .cupon h3 {
      font-size: 1.2rem;
      font-weight: 800;
      margin: 0 0 8px;
      color: #fff;
      text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }

    .cupon p {
      font-size: 0.9rem;
      margin: 4px 0;
      color: rgba(255,255,255,0.9);
      font-weight: 500;
      text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }

    .cupon .vencimiento {
      font-size: 0.8rem;
      margin-top: 6px;
      color: rgba(255,255,255,0.85);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
    }

    /* Bordes tipo ticket */
    .cupon::before,
    .cupon::after {
      content: "";
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 22px;
      height: 22px;
      background: rgba(0,0,0,0.85);
      border-radius: 50%;
    }

    .cupon::before { left: -11px; }
    .cupon::after { right: -11px; }

    /* === POPUP === */
    .popup {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.85);
      backdrop-filter: blur(3px);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .popup-content {
      background: rgba(15,15,25,0.95);
      border: 1px solid #00fff2;
      border-radius: 20px;
      padding: 30px 40px;
      text-align: center;
      box-shadow: 0 0 40px rgba(0,255,242,0.3);
      max-width: 90%;
    }

    .popup-content h2 {
      color: #00fff2;
      margin-bottom: 15px;
      font-weight: 700;
    }

    .popup-content img {
      width: 150px;
      height: 150px;
      margin-bottom: 10px;
    }

    .popup-content button {
      margin-top: 10px;
      background: linear-gradient(90deg, #0037ff, #00fff2);
      border: none;
      border-radius: 25px;
      padding: 8px 18px;
      color: #fff;
      font-weight: 600;
      cursor: pointer;
    }

    .popup-content button:hover {
      transform: scale(1.05);
      box-shadow: 0 0 15px rgba(0,255,242,0.5);
    }

    .btn-volver {
      background: linear-gradient(90deg, #0037ff, #00fff2);
      border: none;
      color: #fff;
      border-radius: 25px;
      padding: 10px 25px;
      font-weight: 600;
      cursor: pointer;
      display: block;
      margin: 40px auto 10px auto;
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
  <?php include 'componentes/menu_cliente.php'; ?>

  <div class="contenedor" id="contenido">
    <header>
      <h1>🎟️ Tus Cupones</h1>
      <div class="aplicar-codigo">
        <input type="text" id="codigoInput" maxlength="5" placeholder="ABCDE">
        <button id="btnAplicar">Aplicar</button>
      </div>
    </header>

    <div id="listaCupones" class="grid-cupones">
      <p>Cargando cupones...</p>
    </div>

    <button class="btn-volver" onclick="window.location.href='panel_cliente.php'">Volver al Panel</button>
  </div>

  <div class="popup" id="popupQR">
    <div class="popup-content">
      <h2 id="popupTitulo"></h2>
      <img id="popupQRimg" src="" alt="QR del Cupón">
      <p id="popupCodigo"></p>
      <button id="popupCerrar">Cerrar</button>
    </div>
  </div>

  <script src="componentes/menu_cliente.js" defer></script>

  <script>
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
          div.classList.add(c.tipo === "fijo" ? "tipo-fijo" :
                           c.tipo === "random" ? "tipo-random" : "tipo-extra");

          div.innerHTML = `
            <h3>${c.nombre}</h3>
            <p>${c.caracteristicas}</p>
            <p class="vencimiento">Vence: ${c.fecha_vencimiento || "Sin fecha"}</p>
          `;
          div.addEventListener("click", () => abrirPopup(c));
          cont.appendChild(div);
        });
      } catch (err) {
        console.error(err);
        cont.innerHTML = "<p>Error al cargar los cupones.</p>";
      }
    }

    const popup = document.getElementById("popupQR");
    const popupTitulo = document.getElementById("popupTitulo");
    const popupQRimg = document.getElementById("popupQRimg");
    const popupCodigo = document.getElementById("popupCodigo");

    function abrirPopup(cupon) {
      popupTitulo.textContent = cupon.nombre;
      popupQRimg.src = `https://api.qrserver.com/v1/create-qr-code/?data=${cupon.codigo_unico}&size=150x150`;
      popupCodigo.textContent = cupon.codigo_unico;
      popup.style.display = "flex";
    }

    document.getElementById("popupCerrar").addEventListener("click", () => {
      popup.style.display = "none";
    });

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
