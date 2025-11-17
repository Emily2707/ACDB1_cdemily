<?php
/**
 * ============================================================
 *   SISTEMA DE AUTENTICACIÓN (AUTH.PHP)
 * ------------------------------------------------------------
 *  Contiene toda la lógica de:
 *    Registro seguro
 *    Inicio de sesión
 *    Cierre de sesión
 *    Consulta de usuario autenticado
 *
 *  Utiliza:
 *   - PDO (desde /config/database.php)
 *   - password_hash() y password_verify()
 *   - Sesiones seguras
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';

class Auth
{
    private $db;    // Conexión PDO
    private $conn;  // Instancia de conexión

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    /* ============================================================
       ✔ REGISTRAR USUARIO
       ------------------------------------------------------------
       - Recibe: nombre, correo, contraseña (sin hash)
       - Valida datos básicos
       - Hashea contraseña con BCrypt
       - Inserta de forma segura (prepared statements)
       ============================================================ */
    public function registrarUsuario($nombre, $correo, $contraseña)
    {
        // Validaciones básicas
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El correo no es válido.");
        }

        if (strlen($contraseña) < 6) {
            throw new Exception("La contraseña debe tener al menos 6 caracteres.");
        }

        // Verificar si el correo ya existe
        $sql = "SELECT id FROM usuarios WHERE correo = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$correo]);

        if ($stmt->rowCount() > 0) {
            throw new Exception("El correo ya está registrado.");
        }

        // Hashear contraseña
        $hash = password_hash($contraseña, PASSWORD_DEFAULT);

        // Insertar usuario
        $sql = "INSERT INTO usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([$nombre, $correo, $hash]);
    }

    /* ============================================================
       ✔ INICIO DE SESIÓN
       ------------------------------------------------------------
       - Recibe: correo y contraseña sin hash
       - Busca usuario y compara hash con password_verify()
       - Si es correcto → crea sesión segura
       ============================================================ */
    public function iniciarSesion($correo, $contraseña)
    {
        $sql = "SELECT * FROM usuarios WHERE correo = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$correo]);

        $usuario = $stmt->fetch();

        // Usuario no existe
        if (!$usuario) {
            throw new Exception("Credenciales incorrectas.");
        }

        // Verificar contraseña
        if (!password_verify($contraseña, $usuario['contraseña'])) {
            throw new Exception("Credenciales incorrectas.");
        }

        // Crear sesión segura
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['login_time'] = time();

        return true;
    }

    /* ============================================================
       ✔ CERRAR SESIÓN
       ------------------------------------------------------------
       - Limpia variables
       - Destruye sesión y cookies
       ============================================================ */
    public function cerrarSesion()
    {
        session_unset();
        session_destroy();

        // Evitar que PHP recree cookie automáticamente
        setcookie(session_name(), '', time() - 3600, '/');
    }

    /* ============================================================
       ✔ VERIFICAR SI ESTÁ LOGUEADO
       ------------------------------------------------------------
       - Retorna true/false dependiendo de la sesión
       ============================================================ */
    public function estaLogueado()
    {
        return !empty($_SESSION['usuario_id']);
    }

    /* ============================================================
       ✔ OBTENER USUARIO ACTUAL
       ------------------------------------------------------------
       - Devuelve array con datos del usuario autenticado
       - Si no está logueado → null
       ============================================================ */
    public function obtenerUsuarioActual()
    {
        if (!$this->estaLogueado()) {
            return null;
        }

        $sql = "SELECT * FROM usuarios WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_SESSION['usuario_id']]);

        return $stmt->fetch(); // Retorna array asociativo del usuario
    }
}

