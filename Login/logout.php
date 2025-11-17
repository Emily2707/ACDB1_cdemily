<?php
/**
 * CIERRE DE SESIÓN - DESTRUCCIÓN SEGURA
 * 
 *  SEGURIDAD: Destrucción completa de la sesión
 *  REDIRECCIÓN: Al login con mensaje de confirmación
 *  LIMPIEZA: Elimina todos los datos de sesión
 * 
 *  ACCESO: http://localhost/Login/logout.php
 */

// Incluir archivos necesarios
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Crear instancia de Auth para usar sus métodos
$auth = new Auth();

// ==================== DESTRUCCIÓN SEGURA DE LA SESIÓN ====================

/**
 * PROCESO DE CIERRE SEGURO:
 * 1. Cerrar sesión usando el método de la clase Auth
 * 2. Iniciar nueva sesión para mensaje flash
 * 3. Establecer mensaje de éxito
 * 4. Redirigir al login
 */

// Cerrar sesión (limpia $_SESSION y destruye la sesión)
$auth->cerrarSesion();

// Iniciar nueva sesión para el mensaje flash
session_start();

// Establecer mensaje de éxito
setSuccess(" ¡Sesión cerrada correctamente! Esperamos verte pronto.");

// Redirigir al login
redirect('pages/login.php');

?>
