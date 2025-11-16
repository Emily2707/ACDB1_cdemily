<?php
/**
 * PÁGINA PRINCIPAL - REDIRECCIÓN AUTOMÁTICA
 * 
 * LÓGICA: Redirige usuarios basándose en su estado de autenticación
 * EFICIENCIA: No contiene HTML, solo lógica de redirección
 */

require_once 'includes/functions.php';

// Redirigir basado en el estado de autenticación
if (isLoggedIn()) {
    redirect('pages/dashboard.php');  // Usuarios logueados → Dashboard
} else {
    redirect('pages/login.php');      // Usuarios no logueados → Login
}

// No se necesita contenido HTML ya que es solo redirección
?>