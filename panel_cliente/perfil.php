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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Perfil | El Club del Berrinche</title>

  <!-- Fuentes y estilos generales -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="componentes/menu_cliente.css" />

  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: url("../assets/img/fondo-hexagonos.jpg") no-repeat center center fixed;
      background-size: cover;
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
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

    .perfil-container {
      position: relative;
      background: rgba(10, 10, 20, 0.9);
      border-radius: 35px;
      padding: 40px;
      width: 90%;
      max-width: 600px;
      text-align: center;
      box-shadow: 0 0 60px rgba(0,255,242,0.3), 0 0 100px rgba(106,0,255,0.25);
      border: 1px solid rgba(0,255,242,0.25);
      z-index: 2;
      margin-top: 80px;
    }

    h1 {
      color: #00fff2;
      text-shadow: 0 0 25px #00fff2, 0 0 40px #4b00ff;
      margin-bottom: 25px;
    }

    .avatar-area {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 25px;
    }

    #userAvatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 2px solid #00fff2;
      box-shadow: 0 0 20px rgba(0,255,242,0.4);
      object-fit: cover;
      margin-bottom: 10px;
    }

    .btn-avatar {
      background: linear-gradient(90deg, #0037ff, #00fff2);
      border: none;
      border-radius: 25px;
      color: #fff;
      font-weight: 600;
      padding: 10px 25px;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn-avatar:hover {
      transform: scale(1.05);
      box-shadow: 0 0 25px rgba(0,255,242,0.6);
    }

    label {
      display: block;
      text-align: left;
      color: #00fff2;
      margin: 10px 0 5px;
      font-weight: 600;
    }

    input {
      width: 100%;
      padding: 10px;
      border-radius: 10px;
      border: 1px solid rgba(0,255,242,0.4);
      background: rgba(255,255,255,0.1);
      color: #fff;
      font-size: 1rem;
    }

    input:disabled {
      opacity: 0.7;
    }

    .botones {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 25px;
    }

    button {
      background: linear-gradient(90deg, #0037ff, #00fff2);
      border: none;
      color: #fff;
      font-weight: 600;
      border-radius: 25px;
      padding: 10px 25px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      transform: scale(1.05);
      box-shadow: 0 0 25px rgba(0,255,242,0.6);
    }

    /* === MODAL === */
    .modal {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.85);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 300;
    }

    .modal.active { display: flex; }

    .modal-content {
      background: rgba(10,10,20,0.95);
      border-radius: 25px;
      padding: 25px;
      width: 90%;
      max-width: 400px;
      text-align: center;
      border: 1px solid rgba(0,255,242,0.3);
      box-shadow: 0 0 40px rgba(0,255,242,0.3);
    }

    .modal-content h2 { color: #00fff2; margin-bottom: 15px; }
    .modal-content button { width: 100%; margin: 8px 0; }

    .avatar-gallery {
      display: flex;
      justify-content: center;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 10px;
    }

    .avatar-gallery img {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      border: 2px solid rgba(0,255,242,0.4);
      cursor: pointer;
      transition: 0.3s;
      object-fit: cover;
    }

    .avatar-gallery img:hover {
      transform: scale(1.1);
      border-color: #00fff2;
      box-shadow: 0 0 15px rgba(0,255,242,0.4);
    }

    video {
      width: 100%;
      border-radius: 15px;
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <div class="overlay"></div>

  <!-- ✅ Menú lateral desde el componente -->
  <?php include 'componentes/menu_cliente.php'; ?>

  <!-- Contenedor del perfil -->
  <div class="perfil-container">
    <h1>Tu Perfil</h1>
    <div class="avatar-area">
      <img id="userAvatar" src="../assets/img/avatars/avatar1.png" alt="Avatar">
      <button type="button" class="btn-avatar" id="btnCambiarAvatar">📸 Cambiar Avatar / Selfie</button>
    </div>

    <form id="perfilForm">
      <input type="hidden" id="selfie_avatar" name="selfie_avatar">

      <label>Nombre o Apodo</label>
      <input type="text" id="nombre" name="nombre" disabled>

      <label>Teléfono</label>
      <input type="text" id="telefono" name="telefono" disabled>

      <label>Email</label>
      <input type="email" id="email" name="email" disabled>

      <div class="botones">
        <button type="button" id="btnEditar">Editar</button>
        <button type="submit" id="btnGuardar" disabled>Guardar</button>
      </div>
    </form>
  </div>

  <!-- Modal de cambio de avatar -->
  <div class="modal" id="modalAvatar">
    <div class="modal-content">
      <h2>Elegí una opción</h2>
      <button id="btnSelfie">📷 Sacar Selfie</button>
      <button id="btnElegirAvatar">🧑‍🎨 Elegir Avatar</button>
      <video id="videoSelfie" autoplay playsinline style="display:none;"></video>
      <div class="avatar-gallery" id="avatarGallery" style="display:none;">
        <img src="../assets/img/avatars/avatar1.png">
        <img src="../assets/img/avatars/avatar2.png">
        <img src="../assets/img/avatars/avatar3.png">
        <img src="../assets/img/avatars/avatar4.png">
        <img src="../assets/img/avatars/avatar5.png">
      </div>
      <button id="btnCerrarModal" style="margin-top:15px;">❌ Cerrar</button>
    </div>
  </div>

  <script src="componentes/menu_cliente.js" defer></script>

  <script>
    // --- Modal y edición de perfil ---
    const modal = document.getElementById("modalAvatar");
    const btnCambiarAvatar = document.getElementById("btnCambiarAvatar");
    const btnSelfie = document.getElementById("btnSelfie");
    const btnElegirAvatar = document.getElementById("btnElegirAvatar");
    const video = document.getElementById("videoSelfie");
    const avatarGallery = document.getElementById("avatarGallery");
    const avatar = document.getElementById("userAvatar");
    const hiddenAvatar = document.getElementById("selfie_avatar");

    btnCambiarAvatar.addEventListener("click", () => modal.classList.add("active"));
    document.getElementById("btnCerrarModal").addEventListener("click", () => {
      modal.classList.remove("active");
      avatarGallery.style.display = "none";
      video.style.display = "none";
      if (video.srcObject) video.srcObject.getTracks().forEach(t => t.stop());
    });

    btnSelfie.addEventListener("click", async () => {
      avatarGallery.style.display = "none";
      video.style.display = "block";
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        video.addEventListener("click", () => {
          const canvas = document.createElement("canvas");
          canvas.width = video.videoWidth;
          canvas.height = video.videoHeight;
          const ctx = canvas.getContext("2d");
          ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
          const imgData = canvas.toDataURL("image/png");
          avatar.src = imgData;
          hiddenAvatar.value = imgData;
          modal.classList.remove("active");
          stream.getTracks().forEach(t => t.stop());
        }, { once: true });
      } catch {
        alert("⚠️ No se pudo acceder a la cámara.");
      }
    });

    btnElegirAvatar.addEventListener("click", () => {
      video.style.display = "none";
      avatarGallery.style.display = "flex";
    });

    document.querySelectorAll("#avatarGallery img").forEach(img => {
      img.addEventListener("click", () => {
        avatar.src = img.src;
        hiddenAvatar.value = img.src;
        modal.classList.remove("active");
      });
    });

    // --- Edición del perfil ---
    const form = document.getElementById("perfilForm");
    const btnEditar = document.getElementById("btnEditar");
    const btnGuardar = document.getElementById("btnGuardar");

    btnEditar.addEventListener("click", () => {
      document.querySelectorAll("#perfilForm input:not([type=hidden])").forEach(i => i.disabled = false);
      btnGuardar.disabled = false;
    });

    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      const resp = await fetch("../php/panel_cliente/perfil_actualizar.php", {
        method: "POST",
        body: formData
      });
      const data = await resp.json();
      alert(data.message);
      if (data.success) window.location.reload();
    });
  </script>
</body>
</html>
