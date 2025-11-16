<?php
/**
 * ============================================================
 *  LOGIN.PHP — Página de Inicio de Sesión
    * ------------------------------------------------------------ *  - Usa Math Captcha local (sin reCAPTCHA externo)
 * ============================================================
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$auth = new Auth();

// Si el usuario ya está logueado, redirigir inmediatamente
if ($auth->estaLogueado()) {
    redirect('dashboard.php');
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        $correo      = sanitizeInput($_POST['correo'] ?? '');
        $contraseña  = $_POST['contraseña'] ?? '';
        $math_input  = $_POST['math_captcha'] ?? '';

        // Validación básica
        if (empty($correo) || empty($contraseña)) {
            throw new Exception("Todos los campos son obligatorios.");
        }

        // Validación CAPTCHA Matemático
        if ($math_input === '' || !verifyMathCaptcha($math_input)) {
            throw new Exception("Completa correctamente la verificación de seguridad.");
        }

        // Intento de inicio de sesión
        if ($auth->iniciarSesion($correo, $contraseña)) {
            $usuario = $auth->obtenerUsuarioActual();
            setSuccess("👋 ¡Bienvenido de nuevo, {$usuario['nombre']}!");
            redirect('dashboard.php');
        }

        throw new Exception("Credenciales incorrectas.");

    } catch (Exception $e) {
        setError($e->getMessage());

        // Guardar correo para repoblar el input
        $_SESSION['login_correo'] = $correo;
    }
}

$saved_correo = $_SESSION['login_correo'] ?? '';
unset($_SESSION['login_correo']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Autenticación</title>
    
    <!-- Bootstrap solo para grid/utilidades -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ==========================================
         CSS INLINE — Optimizando pero sin eliminar
       ========================================== -->
    <style>
        /* Fondo general */
        body {
            background-color: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;
            min-height: 100vh;
        }

        /* Contenedor principal de login */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Wrapper para dividir marca / formulario */
        .login-wrapper {
            display: flex;
            gap: 40px;
            max-width: 1200px;
            width: 100%;
            align-items: center;
            justify-content: center;
        }

        /* Columna izquierda (Marca) */
        .login-brand {
            flex: 1;
            padding: 20px;
        }

        /* Título estilo Facebook */
        .facebook-logo {
            font-size: 56px;
            font-weight: 800;
            color: #1877f2;
            margin-bottom: 20px;
        }

        /* Subtítulo */
        .facebook-tagline {
            font-size: 28px;
            color: #0a66c2;
            font-weight: 500;
            line-height: 1.3;
        }

        /* Caja central del formulario */
        .login-form-wrapper {
            flex: 0 1 396px;
            width: 100%;
        }

        /* Tarjeta del formulario */
        .login-card {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 
                0 2px 4px rgba(0, 0, 0, 0.1), 
                0 8px 16px rgba(0, 0, 0, 0.1);
        }

        /* Botón mostrar/ocultar contraseña */
        .toggle-password {
            background-color: #f5f6f7;
            border: 1px solid #ced0d4;
            color: #0a66c2;
            padding: 11px 16px;
            cursor: pointer;
            font-weight: 600;
        }

        .toggle-password:hover {
            background-color: #ebedf0;
        }

        /* Botón Iniciar Sesión */
        .btn-login {
            width: 100%;
            padding: 8px 16px;
            background: linear-gradient(180deg, #0a66c2 0%, #0952aa 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-login:hover {
            background: linear-gradient(180deg, #0952aa 0%, #084299 100%);
        }
    </style>

</head>

<body>

    <div class="login-container">
        <div class="login-wrapper">

            <!-- Sección de marca -->
            <div class="login-brand">
                <div class="facebook-logo">Acceso</div>
                <div class="facebook-tagline">Bienvenido de nuevo</div>
            </div>

            <!-- Sección del formulario -->
            <div class="login-form-wrapper">

                <div class="login-card">

                    <!-- Mensajes flash -->
                    <?php displayMessage(); ?>

                    <form method="POST" action="" novalidate>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <input type="email"
                                   class="form-control"
                                   id="correo"
                                   name="correo"
                                   value="<?= htmlspecialchars($saved_correo) ?>"
                                   placeholder="Correo Electrónico"
                                   required>
                        </div>

                        <!-- CONTRASEÑA + botón mostrar -->
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="password"
                                       class="form-control"
                                       id="contraseña"
                                       name="contraseña"
                                       placeholder="Contraseña"
                                       required>

                                <button type="button"
                                        class="btn toggle-password"
                                        onclick="togglePassword('contraseña')">
                                    <span id="toggle-text">Ver</span>
                                </button>
                            </div>
                        </div>

                        <!-- CAPTCHA matemático -->
                        <div class="form-group mt-2">
                            <?php $math_question = generateMathCaptcha(); ?>
                            
                            <label for="math_captcha" class="fw-bold" style="font-size:14px;">
                                Verificación:
                            </label>

                            <input type="text"
                                   name="math_captcha"
                                   id="math_captcha"
                                   class="form-control"
                                   placeholder="Responde: <?= $math_question; ?>">
                        </div>

                        <!-- BOTÓN LOGIN -->
                        <button type="submit" class="btn-login mt-3">
                            Iniciar Sesión
                        </button>

                    </form>
                </div>

                <!-- Caja con enlace a registro -->
                <div class="login-card text-center">
                    <p style="margin:0;color:#65676b;">¿No tienes cuenta?
                        <a href="register.php" style="color:#0a66c2;font-weight:600;">Crear una cuenta</a>
                    </p>
                </div>

            </div>
        </div>
    </div>

<!-- Script mostrar/ocultar contraseña -->
<script>
function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    const text = document.getElementById('toggle-text');

    if (input.type === 'password') {
        input.type = 'text';
        text.textContent = 'Ocultar';
    } else {
        input.type = 'password';
        text.textContent = 'Ver';
    }
}
</script>

</body>
</html>
