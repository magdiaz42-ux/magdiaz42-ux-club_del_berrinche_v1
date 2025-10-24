#!/bin/bash
echo "==========================================="
echo " 🚀 SUBIENDO CAMBIOS A GITHUB"
echo "==========================================="

# Verificar cambios
git status

# Agregar todos los archivos
echo "🔹 Agregando archivos nuevos y modificados..."
git add .

# Commit automático con fecha y hora
git commit -m "Actualización automática $(date '+%Y-%m-%d %H:%M:%S')"

# Subir cambios
echo "🔹 Subiendo al repositorio remoto..."
git push origin main

echo "✅ Actualización completada correctamente."
