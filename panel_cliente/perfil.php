<?php
session_start();
require_once("../php/conexion_auto.php");

if (!isset($_SESSION['id_usuario'])) {
  header("Location: ../login.html");
  exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_apodo = "Cliente";
$telefono = "";
$email = "";
$avatar = "../assets/img/avatars/avatar_default.png";

// 🔹 Traer datos desde la base
$query = "SELECT nombre_apodo, telefono, email, selfie_avatar FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows > 0) {
  $user = $resultado->fetch_assoc();
  $nombre_apodo = htmlspecialchars($user['nombre_apodo'] ?? "Cliente");
  $telefono = htmlspecialchars($user['telefono'] ?? "");
  $email = htmlspecialchars($user['email'] ?? "");
  if (!empty($user['selfie_avatar'])) {
    $avatar = "../" . ltrim($user['selfie_avatar'], '/');
  }
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Perfil | El Club del Berrinche</title>

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
      animation: pulseGlow 2.5s infinite alternate;
    }

    @keyframes pulseGlow {
      from { box-shadow: 0 0 10px rgba(0,255,242,0.3), 0 0 25px rgba(0,255,242,0.2); }
      to   { box-shadow: 0 0 25px rgba(0,255,242,0.6), 0 0 50px rgba(0,255,242,0.4); }
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

    .nombre-usuario {
      font-size: 1.2rem;
      font-weight: 600;
      color: #00fff2;
      margin-bottom: 20px;
      text-shadow: 0 0 10px #00fff2, 0 0 25px #4b00ff;
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
      align-items: center;
      gap: 15px;
      margin-top: 25px;
    }

    .botones button {
      background: linear-gradient(90deg, #0037ff, #00fff2);
      border: none;
      color: #fff;
      font-weight: 600;
      border-radius: 25px;
      padding: 12px 28px;
      cursor: pointer;
      font-size: 1rem;
      letter-spacing: 0.5px;
      box-shadow: 0 0 20px rgba(0,255,242,0.4);
      transition: all 0.3s ease;
    }

    .botones button:hover {
      transform: scale(1.05);
      box-shadow: 0 0 30px rgba(0,255,242,0.6);
    }

    .botones button:disabled {
      background: rgba(255,255,255,0.1);
      color: rgba(255,255,255,0.4);
      cursor: not-allowed;
      box-shadow: none;
    }

    /* --- MODAL --- */
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

    .modal-content button {
      width: 100%;
      margin: 8px 0;
      border-radius: 25px;
      background: linear-gradient(90deg,#0037ff,#00fff2);
      border: none;
      color: #fff;
      font-weight: 600;
      padding: 10px;
      cursor: pointer;
      transition: 0.3s;
    }

    .modal-content button:hover {
      transform: scale(1.05);
      box-shadow: 0 0 20px rgba(0,255,242,0.4);
    }

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
  <?php include 'componentes/menu_cliente.php'; ?>

  <div class="perfil-container">
    <h1>Tu Perfil</h1>

    <div class="avatar-area">
      <img id="userAvatar" src="<?= $avatar ?>" alt="Avatar">
      <button type="button" class="btn-avatar" id="btnCambiarAvatar">📸 Cambiar Avatar / Selfie</button>
      <div class="nombre-usuario"><?= $nombre_apodo ?></div>
    </div>

    <form id="perfilForm">
      <input type="hidden" id="selfie_avatar" name="selfie_avatar" value="">

      <label>Nombre o Apodo</label>
      <input type="text" id="nombre" name="nombre" value="<?= $nombre_apodo ?>" disabled>

      <label>Teléfono</label>
      <input type="text" id="telefono" name="telefono" value="<?= $telefono ?>" disabled>

      <label>Email</label>
      <input type="email" id="email" name="email" value="<?= $email ?>" disabled>

      <div class="botones">
        <button type="button" id="btnEditar">✏️ Editar</button>
        <button type="submit" id="btnGuardar" disabled>💾 Guardar</button>
      </div>
    </form>
  </div>

  <!-- 🔹 Modal cambio de avatar -->
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
  document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("perfilForm");
    const btnEditar = document.getElementById("btnEditar");
    const btnGuardar = document.getElementById("btnGuardar");
    const btnCambiarAvatar = document.getElementById("btnCambiarAvatar");
    const inputNombre = document.getElementById("nombre");
    const inputTel = document.getElementById("telefono");
    const inputEmail = document.getElementById("email");
    const inputAvatar = document.getElementById("selfie_avatar");
    const avatarImg = document.getElementById("userAvatar");

    const modal = document.getElementById("modalAvatar");
    const btnSelfie = document.getElementById("btnSelfie");
    const btnElegirAvatar = document.getElementById("btnElegirAvatar");
    const video = document.getElementById("videoSelfie");
    const avatarGallery = document.getElementById("avatarGallery");

    let editMode = false;

    function setEditMode(on) {
      editMode = on;
      [inputNombre, inputTel, inputEmail].forEach(i => i.disabled = !on);
      btnGuardar.disabled = !on;
      btnEditar.textContent = on ? "Cancelar" : "✏️ Editar";
      if (on) inputNombre.focus();
    }

    btnEditar.addEventListener("click", () => setEditMode(!editMode));

    // === Modal Avatar ===
    btnCambiarAvatar.addEventListener("click", () => modal.classList.add("active"));
    document.getElementById("btnCerrarModal").addEventListener("click", () => {
      modal.classList.remove("active");
      video.style.display = "none";
      avatarGallery.style.display = "none";
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
          avatarImg.src = imgData;
          inputAvatar.value = imgData;
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
        avatarImg.src = img.src;
        inputAvatar.value = img.src;
        modal.classList.remove("active");
      });
    });

    // === Guardar Perfil ===
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      if (!editMode) return;

      const formData = new FormData(form);
      try {
        const res = await fetch("../php/panel_cliente/perfil_actualizar.php", {
          method: "POST",
          body: formData
        });
        const data = await res.json();
        if (data.success) {
          alert("✅ Perfil actualizado correctamente");
          setEditMode(false);
        } else {
          alert("⚠️ " + data.message);
        }
      } catch {
        alert("❌ Error al guardar el perfil.");
      }
    });
  });
  </script>
</body>
</html>
