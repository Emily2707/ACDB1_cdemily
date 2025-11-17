<?php
/**
 * ============================================================
 *  FUNCIONES AUXILIARES DEL SISTEMA
 * ------------------------------------------------------------
 *  Este archivo contiene:
 *   ✔ Manejo de sesiones y mensajes flash
 *   ✔ Sanitización de datos
 *   ✔ Redirecciones seguras
 *   ✔ Math CAPTCHA local
 * ============================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ============================================================
   SANITIZAR DATOS
   ============================================================ */
function sanitizeInput($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/* ============================================================
   REDIRECCIÓN SEGURA
   ============================================================ */
function redirect($url)
{
    header("Location: $url");
    exit;
}

/* ============================================================
   MENSAJES FLASH
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
    CAPTCHA LOCAL
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

    $correct = intval($input) === intval($_SESSION['captcha_answer']);
    unset($_SESSION['captcha_answer']); 

    return $correct;
}
