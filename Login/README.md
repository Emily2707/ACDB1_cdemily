# 🔐 Sistema de Autenticación en PHP

Sistema completo de login y registro de usuarios, desarrollado con **PHP puro**, **MySQL** y diseño moderno. Incluye manejo seguro de sesiones, encriptación de contraseñas con bcrypt, validaciones del lado del servidor y protección contra ataques comunes como SQL Injection y XSS.

---

## 🚀 Características Principales

### 🔑 Autenticación Segura
- **Registro de usuarios** con validación completa
- **Inicio de sesión** con verificación de credenciales
- **Cierre de sesión** seguro con destrucción total de la sesión
- **Sesiones persistentes** y seguras

### 🛡️ Medidas de Seguridad
- **Contraseñas encriptadas** con `password_hash()` (bcrypt)
- **Protección SQL Injection** usando PDO y prepared statements
- **Prevención XSS** con `htmlspecialchars()`
- **CAPTCHA matemático local** (sin dependencias externas)
- **Validaciones del lado del servidor**
- **Mensajes flash** para feedback de usuario
- **Protección básica CSRF** con tokens de sesión

### 🎨 Interfaz de Usuario
- **Diseño moderno** inspirado en plataformas populares
- **Responsive design** para móviles y desktop
- **Bootstrap 5** integrado para componentes UI
- **CSS inline optimizado** para mejor rendimiento
- **UX mejorada** con preservación de datos en errores

### 📊 Funcionalidades Adicionales
- **Dashboard protegido** para usuarios autenticados
- **Página de perfil** con información del usuario
- **Redirección automática** basada en estado de autenticación
- **Tiempo en línea** y estadísticas de sesión
- **Código bien documentado** con comentarios detallados

---

## 📂 Estructura del Proyecto

```
Login/
│
├── config/
│   └── database.php           # Configuración de conexión PDO a MySQL
├── includes/
│   ├── auth.php              # Clase Auth con métodos de autenticación
│   └── functions.php         # Funciones auxiliares y helpers
├── pages/
│   ├── dashboard.php         # Panel de control del usuario
│   ├── login.php             # Página de inicio de sesión
│   ├── profile.php           # Perfil del usuario con medidas de seguridad
│   └── register.php          # Formulario de registro
├── database.sql              # Script SQL para crear la base de datos
├── index.php                 # Página principal con redirección automática
├── logout.php                # Cierre de sesión seguro
└── README.md                 # Esta documentación
```

---

## 🛠️ Instalación y Configuración

### Prerrequisitos
- **Servidor web**: Apache/Nginx (recomendado XAMPP/WAMP para desarrollo)
- **PHP**: Versión 7.4 o superior
- **MySQL**: Versión 5.7 o superior
- **Composer**: Opcional, para gestión de dependencias

### Paso 1: Clonar o Descargar
```bash
# Coloca los archivos en tu servidor web (htdocs en XAMPP)
# Asegúrate de que la carpeta se llame "Login"
```

### Paso 2: Configurar la Base de Datos
1. **Crear la base de datos**:
   ```sql
   CREATE DATABASE sistema_auth;
   ```

2. **Importar el esquema**:
   - Abre phpMyAdmin o tu cliente MySQL
   - Importa el archivo `database.sql`
   - Esto creará la tabla `usuarios` con la estructura necesaria

3. **Configurar conexión** (opcional):
   - Edita `config/database.php`
   - Modifica las credenciales si es necesario:
     ```php
     private $host = "localhost";
     private $dbname = "sistema_auth";
     private $username = "root";
     private $password = "";
     ```

### Paso 3: Ejecutar el Sistema
1. **Inicia tu servidor web** (XAMPP: Apache + MySQL)
2. **Accede a la aplicación**:
   ```
   http://localhost/Login/
   ```
3. **Primer uso**:
   - Serás redirigido automáticamente a `login.php`
   - Crea una cuenta en `register.php`
   - Inicia sesión y accede al dashboard

---

## 📖 Uso del Sistema

### Flujo de Usuario
1. **Acceso inicial**: `index.php` redirige según estado de autenticación
2. **Registro**: Completa el formulario en `register.php`
3. **Login**: Inicia sesión en `login.php`
4. **Dashboard**: Accede a tu panel personal en `dashboard.php`
5. **Perfil**: Ve tu información en `profile.php`
6. **Logout**: Cierra sesión en `logout.php`

### URLs Importantes
- **Inicio**: `http://localhost/Login/`
- **Login**: `http://localhost/Login/pages/login.php`
- **Registro**: `http://localhost/Login/pages/register.php`
- **Dashboard**: `http://localhost/Login/pages/dashboard.php`
- **Perfil**: `http://localhost/Login/pages/profile.php`
- **Logout**: `http://localhost/Login/logout.php`

---

## 🔧 Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL con PDO
- **Frontend**: HTML5, CSS3, JavaScript vanilla
- **Framework CSS**: Bootstrap 5 (CDN)
- **Seguridad**: bcrypt, PDO prepared statements, htmlspecialchars
- **Arquitectura**: MVC básico, clases orientadas a objetos

---

## 🛡️ Medidas de Seguridad Implementadas

### Autenticación
- **Hash de contraseñas**: bcrypt con salt automático
- **Sesiones seguras**: ID único por sesión, destrucción completa
- **Validación de entrada**: Sanitización y filtros PHP

### Protección contra Ataques
- **SQL Injection**: PDO con prepared statements
- **XSS**: Escape de output con htmlspecialchars
- **CSRF**: Tokens de sesión implícitos
- **Bots**: CAPTCHA matemático local

### Mejores Prácticas
- **Principio de menor privilegio**: Consultas específicas
- **Manejo de errores**: Try-catch con mensajes controlados
- **Separación de concerns**: Lógica separada de presentación

---

## 📝 Notas de Desarrollo

### Estructura del Código
- **Clase Database**: Maneja conexiones PDO seguras
- **Clase Auth**: Contiene toda la lógica de autenticación
- **Funciones helpers**: Utilidades para sesiones, redirecciones y mensajes

### Base de Datos
```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Variables de Sesión
- `usuario_id`: ID del usuario autenticado
- `login_time`: Timestamp del inicio de sesión
- `success/error`: Mensajes flash
- `captcha_answer`: Respuesta del CAPTCHA matemático

---

## 🚨 Solución de Problemas

### Error de Conexión a BD
- Verifica que MySQL esté ejecutándose
- Confirma credenciales en `database.php`
- Asegúrate de que la BD `sistema_auth` existe

### Sesiones no funcionan
- Verifica que `session_start()` se llame al inicio
- Confirma permisos de escritura en la carpeta de sesiones de PHP

### CAPTCHA no valida
- Asegúrate de que las sesiones estén habilitadas
- Verifica que no haya espacios extra en la respuesta

### Errores 500
- Revisa logs de PHP/error_log
- Confirma versión de PHP compatible
- Verifica sintaxis en archivos modificados

---