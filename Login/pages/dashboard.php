<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAuth();

$auth = new Auth();
$usuario = $auth->obtenerUsuarioActual();

$login_time = $_SESSION['login_time'] ?? time();
$minutes_online = floor((time() - $login_time) / 60);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Autenticación</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

        /* === REPARACIÓN DEL ESTILO GENERAL === */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a5acd, #4c57e5);
            min-height: 100vh;
        }

        /* === NAVBAR FIJA ARRIBA === */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #ffffffdd;
            backdrop-filter: blur(4px);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            z-index: 999;
        }

        .navbar a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            margin-left: 15px;
        }

        /* === CONTENIDO CENTRADO === */
        .dashboard {
            max-width: 900px;
            margin: 120px auto 50px auto; /* separa del navbar */
            padding: 0 20px;
        }

        /* === TARJETAS === */
        .card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            margin-bottom: 30px;
        }

        .info-list li {
            list-style: none;
            padding: 10px 12px;
            background: #f5f7fa;
            margin-bottom: 7px;
            border-radius: 6px;
        }

        .badge {
            background: #4c57e5;
            padding: 4px 8px;
            border-radius: 5px;
            color: white;
        }

        .btn {
            background: #4c57e5;
            color: #fff;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            display: block;
            text-align: center;
        }

        .btn-danger {
            background: #dc3545 !important;
        }

    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-brand">🔐 Sistema de Autenticación</div>
        <div>
            Hola, <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>
            <a href="profile.php">👤 Mi Perfil</a>
            <a href="../logout.php">🚪 Cerrar Sesión</a>
        </div>
    </nav>


    <div class="dashboard">

        <div class="card">
            <h2>📊 Panel de Control</h2>
            <?php displayMessage(); ?>

            <h3>👋 ¡Bienvenido de nuevo!</h3>
            <p>Has iniciado sesión correctamente en el sistema.</p>
        </div>

        <div class="card">
            <h3>📋 Información de tu Cuenta</h3>
            <ul class="info-list">
                <li><strong>ID:</strong> <span class="badge">#<?php echo $usuario['id']; ?></span></li>
                <li><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></li>
                <li><strong>Correo:</strong> <?php echo htmlspecialchars($usuario['correo']); ?></li>
                <li><strong>Tiempo en línea:</strong> <?php echo $minutes_online; ?> minutos</li>
                <li><strong>Último acceso:</strong> <?php echo date('d/m/Y H:i:s', $login_time); ?></li>
            </ul>
        </div>

        <div class="card">
            <h3>🔍 Cómo funciona el sistema</h3>
            <p>Tu sesión se mantiene segura mediante:</p>
            <ul>
                <li>session_start()</li>
                <li>Cookies de sesión</li>
                <li>Datos seguros en el servidor</li>
                <li>bcrypt para contraseñas</li>
                <li>Consultas preparadas</li>
            </ul>
        </div>

        <div class="card">
            <h3>🚀 Acciones Rápidas</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <a class="btn" href="profile.php">👤 Mi Perfil</a>
                <a class="btn btn-danger" href="../logout.php">🚪 Cerrar Sesión</a>
            </div>
        </div>

    </div>

</body>
</html>

