<?php
/**
 * PÁGINA PRINCIPAL
 * Redirige según el estado del usuario
 */

require_once 'includes/functions.php';
require_once 'includes/Auth.php';

$auth = new Auth();

if ($auth->estaLogueado()) {
    redirect('pages/dashboard.php');
} else {
    redirect('pages/login.php');
}
