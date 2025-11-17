<?php
/**
 * ============================================================
 *  🔐 SISTEMA DE AUTENTICACIÓN (AUTH.PHP)
 * ------------------------------------------------------------
 *  Contiene:
 *   ✔ Registro de usuario
 *   ✔ Inicio de sesión
 *   ✔ Cierre de sesión
 *   ✔ Verificación de estado de sesión
 *   ✔ Obtener usuario actual
 *   ✔ Protección de páginas con requireAuth()
 *
 *  Usa conexión PDO desde config/database.php
 * ============================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Iniciar sesión si no está activa
}

require_once __DIR__ . '/../config/database.php'; // Conexión a BD


class Auth
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    /* ============================================================
       ✔ REGISTRAR USUARIO
       ============================================================ */
    public function registrarUsuario($nombre, $correo, $contraseña)
    {
        // Validaciones básicas
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Correo inválido.");
        }

        if (strlen($contraseña) < 6) {
            throw new Exception("La contraseña debe tener mínimo 6 caracteres.");
        }

        // Verificar si el correo ya existe
        $sql = "SELECT id FROM usuarios WHERE correo = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$correo]);

        if ($stmt->rowCount() > 0) {
            throw new Exception("El correo ya está registrado.");
        }

        // Hash seguro
        $hash = password_hash($contraseña, PASSWORD_DEFAULT);

        // Insertar
        $sql = "INSERT INTO usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([$nombre, $correo, $hash]);
    }

    /* ============================================================
       ✔ INICIAR SESIÓN
       ============================================================ */
    public function iniciarSesion($correo, $contraseña)
    {
        // Buscar usuario por correo
        $sql = "SELECT * FROM usuarios WHERE correo = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$correo]);

        $usuario = $stmt->fetch();

        // Si no existe
        if (!$usuario) {
            throw new Exception("Credenciales incorrectas.");
        }

        // Verificar contraseña
        if (!password_verify($contraseña, $usuario['contraseña'])) {
            throw new Exception("Credenciales incorrectas.");
        }

        // Guardar datos mínimos en sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_correo'] = $usuario['correo'];
        $_SESSION['login_time'] = time();

        return true;
    }

    /* ============================================================
       ✔ CERRAR SESIÓN
       ============================================================ */
    public function cerrarSesion()
    {
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
    }

    /* ============================================================
       ✔ SABER SI ESTÁ LOGUEADO
       ============================================================ */
    public function estaLogueado()
    {
        return !empty($_SESSION['usuario_id']);
    }

    /* ============================================================
       ✔ OBTENER DATOS DEL USUARIO ACTUAL
       ============================================================ */
    public function obtenerUsuarioActual()
    {
        if (!$this->estaLogueado()) {
            return null;
        }

        $sql = "SELECT * FROM usuarios WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_SESSION['usuario_id']]);

        return $stmt->fetch();
    }
}


/* ============================================================
   ✔ FUNCIÓN GLOBAL requireAuth()
   ------------------------------------------------------------
   Esta función permite proteger páginas como dashboard,
   profile, configuraciones, etc.
   ============================================================ */
function requireAuth()
{
    // Si el usuario NO está logueado → redirigir al login
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../login.php");
        exit();
    }
}
