# Guía de Instalación y Configuración: PoliCom

Esta guía detalla los pasos necesarios para configurar y ejecutar el proyecto **PoliCom** en un nuevo entorno de desarrollo.

## 📋 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente:

- **PHP 8.2 o superior** (con extensiones `mbstring`, `xml`, `bcmath`, `curl`, `sqlite3` activas).
- **Composer** (Gestor de dependencias de PHP).
- **Node.js (LTS)** y **NPM**.
- Un navegador moderno (Chrome, Edge, Firefox).

---

## 🚀 Pasos de Instalación

Sigue este orden estrictamente para garantizar que el sistema funcione correctamente:

### 1. Clonación o Copia del Proyecto
Copia la carpeta del proyecto a tu nuevo computador y abre una terminal dentro de la carpeta raíz.

### 2. Instalación de Dependencias de Backend
Ejecuta el siguiente comando para instalar Laravel y todas sus librerías:
```bash
composer install
```

### 3. Instalación de Dependencias de Frontend
Instala los paquetes necesarios para la interfaz de usuario (Vue, Tailwind, Vite):
```bash
npm install
```

### 4. Configuración del Archivo de Entorno
Crea una copia del archivo de ejemplo para configurar tu entorno local:
```bash
cp .env.example .env
```

### 5. Generación de la Llave de Aplicación
Esto es vital para la seguridad y el cifrado de sesiones:
```bash
php artisan key:generate
```

### 6. Configuración de la Base de Datos (Opción SQLite - Recomendada para pruebas rápidas)
Por defecto, el proyecto está listo para usar SQLite. Solo necesitas crear el archivo de base de datos vacío:

**En Windows (PowerShell):**
```powershell
New-Item -ItemType File -Path database/database.sqlite
```
**En Linux/Mac:**
```bash
touch database/database.sqlite
```

> [!NOTE]
> Si prefieres usar **MySQL**, edita el archivo `.env` y cambia `DB_CONNECTION=sqlite` por `DB_CONNECTION=mysql`, completando los datos de tu servidor local.

### 7. Ejecución de Migraciones
Este comando creará todas las tablas necesarias y cargará los roles iniciales:
```bash
php artisan migrate --seed
```

---

## 💻 Ejecución del Proyecto

Para ver el proyecto en funcionamiento, necesitas correr **dos procesos simultáneamente** (en dos terminales diferentes):

### Terminal A: Servidor PHP
```bash
php artisan serve
```
*Esto te dará una URL, usualmente `http://127.0.0.1:8000`*

### Terminal B: Compilador de Frontend (Vite)
```bash
npm run dev
```
*Esto es necesario para que los estilos y componentes de Vue se carguen correctamente.*

---

## 🛠️ Solución de Problemas Comunes

- **Error de permisos en carpetas:** Asegúrate de que las carpetas `storage` y `bootstrap/cache` tengan permisos de escritura.
- **Vite no carga:** Verifica que no tengas otra aplicación usando el puerto 5173 o reinicia el comando `npm run dev`.
- **Base de datos bloqueada:** Si usas SQLite, asegúrate de que el archivo `database.sqlite` no esté siendo usado por otro programa de edición de base de datos al momento de migrar.
