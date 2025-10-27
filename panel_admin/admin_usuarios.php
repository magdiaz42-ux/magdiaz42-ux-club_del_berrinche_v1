<?php
require_once("../php/verificar_acceso.php");
verificarRol(['admin']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>👥 Usuarios | Club del Berrinche</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="componentes/estilo_admin.css">

  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: url("../assets/img/fondo-hexagonos.jpg") no-repeat center/cover fixed;
      color: #fff;
      overflow-y: auto;
    }

    .overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 0; }

    /* --- CONTENEDOR --- */
    .contenedor {
      position: relative;
      z-index: 1;
      padding: clamp(80px,8vh,100px) 20px 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      transition: margin-left .3s ease;
    }

    .sidebar.active ~ .contenedor { margin-left: 240px; }

    .titulo-principal {
      color: #00fff2;
      text-shadow: 0 0 25px #00fff2, 0 0 50px #4b00ff;
      margin-bottom: 10px;
    }

    .subtitulo { color: #ccc; margin-bottom: 30px; }

    /* --- FILTROS estilo ventas --- */
    .filtros {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 10px;
      margin-bottom: 25px;
    }

    select {
      padding: 8px 12px;
      border-radius: 10px;
      border: 2px solid rgba(0,255,242,0.4);
      background: rgba(255,255,255,0.1);
      color: #fff;
      outline: none;
      transition: all 0.3s ease;
    }
    select:focus {
      border-color: #00fff2;
      box-shadow: 0 0 12px #00fff2;
    }

    .btn {
      background: linear-gradient(90deg,#0037ff,#00fff2);
      border: none;
      color: #fff;
      font-weight: 600;
      padding: 10px 20px;
      border-radius: 25px;
      box-shadow: 0 0 25px rgba(0,255,242,.5);
      cursor: pointer;
      transition: transform .2s, box-shadow .2s;
      text-decoration: none;
    }
    .btn:hover { transform: scale(1.05); box-shadow: 0 0 35px rgba(0,255,242,.7); }

    /* --- TABLA --- */
    .tabla-usuarios {
      width: 100%;
      max-width: 1000px;
      border-collapse: collapse;
      background: rgba(10,10,20,0.9);
      border-radius: 10px;
      box-shadow: 0 0 30px rgba(0,255,242,0.2);
      overflow: hidden;
      margin-top: 10px;
    }
    th, td {
      padding: 10px 8px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      text-align: center;
    }
    th {
      background: rgba(0,255,242,0.15);
      color: #00fff2;
      text-transform: uppercase;
      font-size: 0.85rem;
    }

    .avatar-mini {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      border: 2px solid #00fff2;
      object-fit: cover;
      box-shadow: 0 0 10px rgba(0,255,242,0.3);
    }

    .btn-mini {
      background: linear-gradient(90deg,#ff005e,#ff00b3);
      color: white;
      border: none;
      border-radius: 10px;
      padding: 5px 12px;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all .2s ease;
    }
    .btn-mini:hover {
      transform: scale(1.08);
      box-shadow: 0 0 20px rgba(255,0,100,0.5);
    }

    /* --- MODAL CREAR USUARIO --- */
    .modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.8);
      z-index: 2000;
      justify-content: center;
      align-items: center;
    }
    .modal.active { display: flex; }

    .modal-contenido {
      background: rgba(10,10,25,0.95);
      border-radius: 20px;
      padding: 30px 40px;
      box-shadow: 0 0 40px rgba(0,255,242,0.3);
      width: 90%;
      max-width: 420px;
      text-align: center;
      color: #fff;
    }

    .modal-contenido input, .modal-contenido select {
      width: 100%;
      padding: 10px;
      margin-bottom: 12px;
      border-radius: 10px;
      border: 2px solid rgba(0,255,242,0.4);
      background: rgba(255,255,255,0.1);
      color: #fff;
      text-align: center;
    }

    .avatar-preview {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      border: 2px solid #00fff2;
      object-fit: cover;
      margin-bottom: 15px;
    }

    .avatar-select {
      width: 100%;
      padding: 10px;
      border-radius: 10px;
      background: rgba(255,255,255,0.1);
      border: 2px solid rgba(0,255,242,0.3);
      color: #fff;
    }

    .modal-botones {
      display: flex;
      justify-content: space-between;
      margin-top: 10px;
    }
    .btn-cerrar { background: linear-gradient(90deg,#ff005e,#ff00b3); }

    @media(max-width:768px){
      .sidebar.active ~ .contenedor{margin-left:0;}
      .tabla-usuarios{font-size:0.85rem;}
    }
  </style>
</head>

<body>
  <div class="overlay"></div>
  <?php include __DIR__ . "/componentes/menu_admin.php"; ?>

  <div class="contenedor">
    <h1 class="titulo-principal">👥 Administración de Empleados</h1>
    <p class="subtitulo">Gestioná usuarios del sistema y sus permisos</p>

    <div class="filtros">
      <select id="filtroRol">
        <option value="">Filtrar por Rol</option>
        <option value="admin">Administrador</option>
        <option value="cajero">Cajero</option>
        <option value="dj">DJ</option>
        <option value="karaoke">Karaoke</option>
        <option value="cine">Silent Cine</option>
      </select>
      <select id="filtroEstado">
        <option value="">Filtrar por Estado</option>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
      </select>
      <button class="btn" id="btnFiltrar">🔍 Filtrar</button>
      <button class="btn" id="btnNuevo">➕ Nuevo</button>
    </div>

    <table class="tabla-usuarios" id="tablaUsuarios">
      <thead>
        <tr>
          <th>Avatar</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="usuariosBody">
        <tr><td colspan="6" style="text-align:center;">Cargando usuarios...</td></tr>
      </tbody>
    </table>
  </div>

  <!-- 🪟 MODAL NUEVO USUARIO -->
  <div class="modal" id="modalNuevo">
    <div class="modal-contenido">
      <h2>➕ Crear Usuario</h2>

      <img src="../assets/img/avatars/avatar_default.png" id="previewAvatar" class="avatar-preview" alt="Avatar">
      <select id="nuevoAvatar" class="avatar-select">
        <option value="avatar_default.png">Seleccionar avatar</option>
        <option value="avatar_1.png">Avatar 1</option>
        <option value="avatar_2.png">Avatar 2</option>
        <option value="avatar_3.png">Avatar 3</option>
        <option value="avatar_4.png">Avatar 4</option>
      </select>

      <input type="text" id="nuevoNombre" placeholder="Nombre / Apodo" required>
      <input type="email" id="nuevoEmail" placeholder="Correo electrónico" required>
      <input type="password" id="nuevoPass" placeholder="Contraseña" required>
      <select id="nuevoRol" required>
        <option value="">Seleccionar Rol</option>
        <option value="admin">Administrador</option>
        <option value="cajero">Cajero</option>
        <option value="dj">DJ</option>
        <option value="karaoke">Karaoke</option>
        <option value="cine">Silent Cine</option>
      </select>

      <div class="modal-botones">
        <button class="btn" id="btnGuardar">Guardar</button>
        <button class="btn btn-cerrar" id="btnCerrar">Cancelar</button>
      </div>
    </div>
  </div>

  <script>
    const modal = document.getElementById("modalNuevo");
    const preview = document.getElementById("previewAvatar");
    const selectAvatar = document.getElementById("nuevoAvatar");

    document.getElementById("btnNuevo").addEventListener("click", () => modal.classList.add("active"));
    document.getElementById("btnCerrar").addEventListener("click", () => modal.classList.remove("active"));
    selectAvatar.addEventListener("change", e => preview.src = `../assets/img/avatars/${e.target.value}`);

    async function cargarUsuarios(filtros = {}) {
      const query = new URLSearchParams(filtros).toString();
      const res = await fetch(`../php/panel_admin/obtener_usuarios.php?${query}`);
      const data = await res.json();
      const body = document.getElementById("usuariosBody");
      body.innerHTML = "";

      if (data.length > 0) {
        data.forEach(u => {
          const avatar = u.avatar ? `../assets/img/avatars/${u.avatar}` : `../assets/img/avatars/avatar_default.png`;
          body.innerHTML += `
            <tr>
              <td><img src="${avatar}" class="avatar-mini"></td>
              <td>${u.nombre_apodo}</td>
              <td>${u.email}</td>
              <td>${u.rol}</td>
              <td>${u.estado}</td>
              <td><button class="btn-mini" onclick="borrarUsuario(${u.id})">🗑️</button></td>
            </tr>`;
        });
      } else {
        body.innerHTML = "<tr><td colspan='6' style='text-align:center;'>No hay usuarios.</td></tr>";
      }
    }

    document.getElementById("btnFiltrar").addEventListener("click", () => {
      const rol = document.getElementById("filtroRol").value;
      const estado = document.getElementById("filtroEstado").value;
      cargarUsuarios({ rol, estado });
    });

    async function borrarUsuario(id) {
      if (!confirm("¿Seguro que querés borrar este usuario?")) return;
      const res = await fetch(`../php/panel_admin/borrar_usuario.php?id=${id}`);
      const data = await res.json();
      alert(data.mensaje);
      if (data.ok) cargarUsuarios();
    }

    document.getElementById("btnGuardar").addEventListener("click", async () => {
      const nombre = document.getElementById("nuevoNombre").value.trim();
      const email = document.getElementById("nuevoEmail").value.trim();
      const password = document.getElementById("nuevoPass").value.trim();
      const rol = document.getElementById("nuevoRol").value.trim();
      const selfie_avatar = document.getElementById("nuevoAvatar").value.trim();

      if (!nombre || !email || !password || !rol) {
        alert("⚠️ Completá todos los campos");
        return;
      }

      const res = await fetch("../php/panel_admin/crear_usuario.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ nombre_apodo: nombre, email, password, rol, selfie_avatar })
      });

      const data = await res.json();
      alert(data.mensaje);
      if (data.ok) {
        modal.classList.remove("active");
        cargarUsuarios();
      }
    });

    window.onload = () => cargarUsuarios();
  </script>
</body>
</html>
