@echo off
title Actualizador del Club del Berrinche
echo ===========================================
echo   🚀 SUBIENDO CAMBIOS A GITHUB
echo ===========================================
echo.

:: Paso 1: Verificar cambios
git status

:: Paso 2: Agregar todos los archivos modificados
echo.
echo 🔹 Agregando archivos nuevos y modificados...
git add .

:: Paso 3: Crear commit automático con fecha y hora
set FECHA=%date%_%time%
git commit -m "Actualización automática %FECHA%"

:: Paso 4: Subir al repositorio remoto
echo.
echo 🔹 Subiendo al repositorio remoto...
git push origin main

echo.
echo ✅ Actualización completada correctamente.
pause
