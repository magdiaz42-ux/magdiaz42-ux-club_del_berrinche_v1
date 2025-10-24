<?php
// Si el usuario ya está logueado, redirigirlo a su panel
session_start();
if (isset($_SESSION['rol'])) {
  switch ($_SESSION['rol']) {
    case 'admin': header("Location: panel_admin/panel_admin.php"); exit;
    case 'dj': header("Location: panel_dj/panel_dj.php"); exit;
    case 'cajero': header("Location: panel_cajero/panel_cajero.php"); exit;
    case 'karaoke': header("Location: panel_karaoke/panel_karaoke.php"); exit;
    case 'cine': header("Location: panel_cine/panel_cine.php"); exit;
    case 'juegos': header("Location: panel_juegos/panel_juegos.php"); exit;
    default: header("Location: panel_cliente/panel_cliente.php"); exit;
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar sesión | El Club del Berrinche</title>

  <!-- Fuente y estilo base -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css" />

  <style>
    html, body {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100vh;
      font-family: "Poppins", sans-serif;
      background: url("assets/img/fondo-hexagonos.jpg") no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      color: #fff;
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      z-index: 0;
      backdrop-filter: blur(2px);
    }

    .contenedor {
      position: relative;
      z-index: 2;
      background: rgba(10, 10, 20, 0.9);
      border-radius: 40px;
      box-shadow: 0 0 60px rgba(0,255,242,0.3), 0 0 100px rgba(106,0,255,0.25);
      padding: 60px 40px;
      width: 90%;
      max-width: 420px;
      text-align: center;
      animation: fadeIn 0.8s ease-in-out;
      border: 1px solid rgba(0,255,242,0.25);
    }

    h1 {
      color: #00fff2;
      text-shadow: 0 0 25px #00fff2, 0 0 60px #4b00ff;
      font-size: clamp(1.8rem, 4vw, 2.4rem);
      margin-bottom: 10px;
    }

    p {
      color: #ccc;
      margin: 0 0 20px 0;
      font-size: 0.95rem;
    }

    form {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 15px;
      align-items: center;
    }

    .input {
      width: 100%;
      padding: 12px;
      border-radius: 12px;
      border: 2px solid rgba(0, 255, 242, 0.4);
      background: rgba(255, 255, 255, 0.08);
      color: #fff;
      text-align: center;
      outline: none;
      transition: all 0.3s ease;
      font-size: 1rem;
    }

    .input::placeholder { color: #bbb; }

    .input:focus {
      border-color: #00fff2;
      box-shadow: 0 0 12px #00fff2;
    }

    .btn {
      background: linear-gradient(90deg, #0037ff, #00fff2);
      border: none;
      color: #fff;
      font-weight: 600;
      padding: 12px 35px;
      border-radius: 25px;
      box-shadow: 0 0 25px rgba(0, 255, 242, 0.6);
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      width: 100%;
      font-size: 1rem;
      text-transform: uppercase;
    }

    .btn:hover {
      transform: scale(1.05);
      box-shadow: 0 0 40px rgba(0, 255, 242, 0.8);
    }

    a {
      color: #00fff2;
      text-decoration: none;
      font-size: 0.9em;
    }

    a:hover {
      text-shadow: 0 0 10px #00fff2;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
  </style>
</head>

<body>
  <div class="overlay"></div>

  <div class="contenedor">
    <h1>El Club del Berrinche</h1>
    <p>Ingresá tu correo o teléfono para acceder</p>

    <form action="php/login_procesar.php" method="POST">
      <input name="usuario" class="input" placeholder="Correo o teléfono" required />
      <input name="password" type="password" class="input" placeholder="Contraseña" required />
      <button type="submit" class="btn">Entrar</button>
    </form>

    <p><a href="recuperar_password.php">¿Olvidaste tu contraseña?</a></p>
    <p><a href="registro/registro_etapa1_datos.html">¿Todavía no tenés cuenta? Registrate acá</a></p>

    <?php if (isset($_GET['error'])): ?>
      <p style="color:#ff6b6b; margin-top:10px;">
        <?php
          switch ($_GET['error']) {
            case 'campos_vacios': echo '⚠️ Completá todos los campos.'; break;
            case 'pass_incorrecta': echo '❌ Contraseña incorrecta.'; break;
            case 'usuario_inactivo': echo '🚫 Usuario inactivo. Consultá en el local.'; break;
            case 'acceso_denegado': echo '🔒 Acceso no autorizado.'; break;
            default: echo '❌ Error al iniciar sesión.';
          }
        ?>
      </p>
    <?php endif; ?>
  </div>
</body>
</html>
