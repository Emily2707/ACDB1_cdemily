<?php
/**
 * ============================================================
 *   FUNCIONES AUXILIARES DEL SISTEMA
 * ------------------------------------------------------------
 *  Este archivo contiene:
 *    Manejo de sesiones y mensajes flash
 *    Sanitización de datos
 *    Redirecciones seguras
 *    Math CAPTCHA para evitar bots
 * ============================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ============================================================
     FUNCIÓN: Sanitizar datos de entrada
   ------------------------------------------------------------
   - Previene inyecciones XSS
   - Elimina espacios innecesarios
   ============================================================ */
function sanitizeInput($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/* ============================================================
    FUNCIÓN: Redirección segura
   ------------------------------------------------------------
   - Evita reenvío de formularios
   - Corta la ejecución inmediatamente
   ============================================================ */
function redirect($url)
{
    header("Location: $url");
    exit;
}

/* ============================================================
   SISTEMA DE MENSAJES FLASH (EXITO / ERROR)
   ------------------------------------------------------------
   - Permiten mostrar mensajes en la siguiente carga de página
   ============================================================ */
function setSuccess($msg)
{
    $_SESSION['success'] = $msg;
}

function setError($msg)
{
    $_SESSION['error'] = $msg;
}

function displayMessage()
{
    if (!empty($_SESSION['success'])) {
        echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
        unset($_SESSION['success']);
    }

    if (!empty($_SESSION['error'])) {
        echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
    }
}

/* ============================================================
    CAPTCHA MATEMÁTICO LOCAL (ANTI-BOTS)
   ------------------------------------------------------------
   - No depende de Google reCAPTCHA
   - Pregunta simple como: "3 + 5"
   - Se guarda la respuesta correcta en sesión
   ============================================================ */
function generateMathCaptcha()
{
    $a = rand(1, 9);
    $b = rand(1, 9);

    $_SESSION['captcha_answer'] = $a + $b;

    return "$a + $b = ?";
}

function verifyMathCaptcha($input)
{
    if (!isset($_SESSION['captcha_answer'])) {
        return false;
    }

    $isCorrect = intval($input) === intval($_SESSION['captcha_answer']);

    unset($_SESSION['captcha_answer']); // Evita reuso del captcha

    return $isCorrect;
}

/* ============================================================
    FUNCIÓN: Requerir autenticación
   ------------------------------------------------------------
   - Bloquea acceso a páginas privadas
   - Si no está logueado → redirige a login
   ============================================================ */
function requireAuth()
{
    if (empty($_SESSION['usuario_id'])) {
        setError("Debes iniciar sesión para acceder a esta página.");
        redirect('../pages/login.php');
    }
}

