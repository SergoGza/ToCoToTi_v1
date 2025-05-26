# 🌱 ToCoToTi - Todo Cojo, Todo Tiro

> **Una plataforma web de economía circular que conecta personas para compartir objetos**

ToCoToTi es una plataforma web de economía circular que conecta personas para compartir objetos que ya no utilizan, fomentando la reutilización y sostenibilidad. "Todo Cojo, Todo Tiro" representa la filosofía de dar nueva vida a los objetos mediante el intercambio comunitario.

---

## 🔧 Requisitos del Sistema

Para ejecutar este proyecto necesitará:

- **PHP** 8.2 o superior
- **Composer** para la gestión de dependencias de PHP
- **Node.js** 18 o superior con NPM
- **MySQL**

---

## 🚀 Instalación

### 1️⃣ **Configuración Inicial**

```bash
# Clonar el repositorio
git clone https://github.com/SergoGza/ToCoToTi_v1.git
cd ToCoToTi_v1
```

### 2️⃣ **Instalación de Dependencias**

```bash
# Instalar dependencias de PHP
composer install

# Instalar dependencias de JavaScript
npm install
```

### 3️⃣ **Configuración del Entorno**

```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 4️⃣ **Base de Datos**

```bash
# Ejecutar migraciones
php artisan migrate

# Poblar con datos de prueba (opcional)
php artisan db:seed

# Crear enlace simbólico para almacenamiento
php artisan storage:link
```

---

## ⚙️ Configuración del Entorno

El archivo `.env` contiene la configuración principal de la aplicación. La configuración por defecto utiliza ya utiliza MySQL.

Para las funcionalidades de WebSocket y notificaciones en tiempo real, la aplicación está configurada para usar **Laravel Reverb** con la configuración por defecto en `localhost` puerto `8080`.

---

## 🖥️ Ejecución de la Aplicación

Para ejecutar la aplicación correctamente, necesita iniciar **tres servicios simultáneamente**:

### 🌐 **Servidor Web**
```bash
php artisan serve
```
*Aplicación disponible en* `http://localhost:8000`

### 🎨 **Servidor de Desarrollo Frontend**
```bash
npm run dev
```
*Compila y sirve archivos CSS y JavaScript*

### 📡 **Servidor WebSocket**
```bash
php artisan reverb:start
```
*Maneja conexiones en tiempo real para mensajería y notificaciones*

> ✅ **Con estos tres servicios ejecutándose, la aplicación estará completamente funcional.**

---

## 🤖 Comando Automatizado

La aplicación incluye un comando artisan `app:find-matches` que se puede programar para ejecutarse periódicamente y notificar automáticamente a los usuarios sobre nuevas coincidencias entre sus solicitudes y los items disponibles.

```bash
php artisan app:find-matches
```

---

## 📧 Contacto

**Repositorio:** https://github.com/SergoGza/ToCoToTi_v1.git

---

*Construyendo una economía circular, un intercambio a la vez* 🔄
