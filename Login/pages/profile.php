<?php
/**
 * =======================================================
 *              PÁGINA DE PERFIL
 * =======================================================
 *
 *  Requiere sesión activa (requireAuth)
 *  Muestra información del usuario autenticado
 *  Interfaz moderna con Bootstrap 5
 *  Se mantiene el CSS inline original (por tu petición)
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Bloquear acceso a usuarios NO autenticados
requireAuth();

// Obtener información del usuario
$auth = new Auth();
$usuario = $auth->obtenerUsuarioActual();

// Protección extra: si falla consulta, cerrar sesión
if (!$usuario) {
    setError("No se pudo obtener tu información.");
    redirect('../logout.php');
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Sistema de Autenticación</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0f2f5 !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
        }
        .navbar-custom {
            background-color: #0a66c2;
            padding: 14px;
        }
        .navbar-brand, .nav-link, .user-welcome {
            color: white !important;
            font-weight: 500;
        }
        .card-custom {
            border-radius: 10px;
            border: none;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .security-box {
            background: #ffffff;
            border-left: 5px solid #0a66c2;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.06);
        }
        pre {
            border-radius: 10px !important;
            padding: 18px !important;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-custom d-flex justify-content-between">
    <a class="navbar-brand">🔐 Sistema de Autenticación</a>

    <div class="d-flex align-items-center gap-3">
        <span class="user-welcome">👤 <?= htmlspecialchars($usuario['nombre']) ?></span>
        <a href="dashboard.php" class="nav-link">📊 Dashboard</a>
        <a href="../logout.php" class="nav-link">🚪 Cerrar Sesión</a>
    </div>
</nav>

<!-- CONTENIDO -->
<div class="container py-4">

    <?php displayMessage(); ?>

    <!-- Información del Usuario -->
    <div class="card card-custom p-4 mb-4">
        <h2 class="mb-3">👤 Mi Perfil</h2>

        <div class="card card-custom p-3">
            <h4 class="mb-3">📋 Información Personal</h4>

            <ul class="list-group">
                <li class="list-group-item">
                    <strong>ID:</strong> <?= (int)$usuario['id'] ?>
                </li>
                <li class="list-group-item">
                    <strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?>
                </li>
                <li class="list-group-item">
                    <strong>Correo:</strong> <?= htmlspecialchars($usuario['correo']) ?>
                </li>
                <li class="list-group-item">
                    <strong>Miembro desde:</strong>
                    <?= date('d/m/Y', strtotime($usuario['fecha_creacion'] ?? 'now')) ?>
                </li>
            </ul>
        </div>
    </div>

    <!-- Seguridad -->
    <div class="card card-custom p-4 mb-4">
        <h3 class="mb-4">🛡️ Medidas de Seguridad Implementadas</h3>

        <div class="row g-4">

            <!-- HASH -->
            <div class="col-md-6">
                <div class="security-box">
                    <h5>🔐 Hash de Contraseñas</h5>
                    <p><strong>Tecnología:</strong> password_hash() (bcrypt)</p>
                    <ul>
                        <li>Salt único automático</li>
                        <li>Alta resistencia a ataques</li>
                        <li>Estándar recomendado por OWASP</li>
                    </ul>
                </div>
            </div>

            <!-- SQL Injection -->
            <div class="col-md-6">
                <div class="security-box" style="border-left-color: #28a745;">
                    <h5>🛡️ Protección SQL Injection</h5>
                    <p><strong>Uso:</strong> PDO + Prepared Statements</p>
                    <ul>
                        <li>Consultas seguras por parámetros</li>
                        <li>Evita inyección SQL en formularios</li>
                    </ul>
                </div>
            </div>

            <!-- XSS -->
            <div class="col-md-6">
                <div class="security-box" style="border-left-color: #ffc107;">
                    <h5>🚫 Prevención XSS</h5>
                    <p><strong>Uso:</strong> htmlspecialchars()</p>
                    <ul>
                        <li>Filtra HTML malicioso</li>
                        <li>Protección inmediata en salida</li>
                    </ul>
                </div>
            </div>

            <!-- Sesiones -->
            <div class="col-md-6">
                <div class="security-box" style="border-left-color: #dc3545;">
                    <h5>🔑 Manejo Seguro de Sesiones</h5>
                    <p><strong>Uso:</strong> Sesiones nativas PHP</p>
                    <ul>
                        <li>ID aleatorio en cada inicio</li>
                        <li>Destrucción total al cerrar sesión</li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- Código Hash -->
        <div class="mt-4">
            <h4>🔧 Ejemplo: Hash de Contraseña</h4>
            <pre class="bg-dark text-light">
<code>// Crear hash seguro
$hash = password_hash($contraseña, PASSWORD_DEFAULT);

// Verificar hash
password_verify($input, $hash);</code>
            </pre>
        </div>

        <!-- Código SQL Seguro -->
        <div class="mt-4">
            <h4>🔧 Ejemplo: Consulta Segura</h4>
            <pre class="bg-dark text-light">
<code>$sql = "SELECT * FROM usuarios WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$correo]);</code>
            </pre>
        </div>
    </div>

    <div class="text-center">
        <a href="dashboard.php" class="btn btn-primary px-4">← Volver al Dashboard</a>
    </div>

</div>

</body>
</html>
