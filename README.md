# 🎭 El Club del Berrinche V1

**El Club del Berrinche** es una aplicación web híbrida desarrollada en PHP, MySQL, HTML, CSS y JavaScript.  
Está diseñada para funcionar tanto en entorno local (XAMPP) como remoto (InfinityFree), ofreciendo una experiencia interactiva tipo “bar de experiencias” con distintos roles de usuario y módulos temáticos.

---

## 🚀 Características principales

### 🔐 Sistema de Login y Roles
- Inicio de sesión con **correo o teléfono**.
- Validación de credenciales con `password_hash` / `password_verify`.
- Asignación automática de panel según el **rol del usuario**:
  - 🧍 Cliente
  - 💵 Cajero
  - 🎧 DJ
  - 🎤 Karaoke
  - 🎬 Cine
  - 🎮 Juegos
  - 🧑‍💼 Admin

Cada rol accede a su propio panel personalizado.

---

## 🧱 Estructura general del proyecto

