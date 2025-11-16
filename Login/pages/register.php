<?php
/**
 * ============================================================
 *  REGISTRO DE USUARIOS - register.php
 * ============================================================
 *  Funcionalidades principales:
 *  - Registro de usuarios con validación del lado del servidor
 *  - Sanitización, manejo de errores, mensajes flash
 *  - Protección contra CSRF básico (session-based)
 *  - Math CAPTCHA local (sin claves externas)
 *  - Preservación de datos en caso de error para mejor UX
 * ============================================================
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$auth = new Auth();

// Si el usuario ya está autenticado → redirigir
if ($auth->estaLogueado()) {
    redirect('dashboard.php');
}

/**
 * ============================================================
 *  MANEJO DEL FORMULARIO
 * ============================================================
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitización: NO sanitizar contraseñas
        $nombre  = sanitizeInput($_POST['nombre'] ?? '');
        $correo  = sanitizeInput($_POST['correo'] ?? '');
        $pass1   = $_POST['contraseña'] ?? '';
        $pass2   = $_POST['confirmar_contraseña'] ?? '';
        $captcha = $_POST['math_captcha'] ?? '';

        // Validación básica
        if (empty($nombre) || empty($correo) || empty($pass1)) {
            throw new Exception("Todos los campos son obligatorios.");
        }

        // Validación CAPTCHA
        if ($captcha === '' || !verifyMathCaptcha($captcha)) {
            throw new Exception("Resuelve correctamente la verificación de seguridad.");
        }

        // Confirmación de contraseñas
        if ($pass1 !== $pass2) {
            throw new Exception("Las contraseñas no coinciden.");
        }

        // Registro en base de datos
        if ($auth->registrarUsuario($nombre, $correo, $pass1)) {

            setSuccess("🎉 ¡Tu cuenta ha sido creada! Ya puedes iniciar sesión.");
            redirect('login.php');
        }

        throw new Exception("No se pudo completar el registro.");

    } catch (Exception $e) {
        // Guardar mensaje
        setError($e->getMessage());

        // Preservar información
        $_SESSION['form_data'] = [
            'nombre' => $nombre,
            'correo' => $correo
        ];
    }
}

// Recuperar datos preservados
$form = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Autenticación</title>

    <!-- Bootstrap solo para grid/utilidades -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ============================================================
         CSS INLINE (NO SE ELIMINA) — Solo optimizado/ordenado
         ============================================================ -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI',
                         Helvetica, Arial, sans-serif;
            min-height: 100vh;
        }

        /* CONTENEDOR GENERAL */
        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* WRAPPER PRINCIPAL */
        .register-wrapper {
            display: flex;
            gap: 40px;
            max-width: 1200px;
            width: 100%;
            align-items: center;
        }

        /* COLUMNA IZQUIERDA */
        .register-brand {
            flex: 1;
            padding: 20px;
        }

        .facebook-logo {
            font-size: 56px;
            font-weight: 800;
            color: #1877f2;
            margin-bottom: 20px;
        }

        .facebook-tagline {
            font-size: 28px;
            color: #0a66c2;
            font-weight: 500;
            line-height: 1.3;
        }

        /* FORMULARIO */
        .register-form-wrapper {
            flex: 0 1 396px;
            width: 100%;
        }

        .register-card {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow:
                0 2px 4px rgba(0, 0, 0, 0.1),
                0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-control {
            width: 100%;
            padding: 11px 16px;
            font-size: 15px;
            border: 1px solid #ced0d4;
            border-radius: 5px;
            background-color: #f5f6f7;
            transition: all .2s;
            font-family: inherit;
        }

        .form-control:focus {
            background-color: white;
            border-color: #0a66c2;
            box-shadow: 0 0 0 2px rgba(8, 102, 194, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #65676b;
        }

        /* INPUT GROUP PASSWORD */
        .input-group {
            display: flex;
        }

        .input-group .form-control {
            flex: 1;
            border-right: none;
        }

        .input-group .btn {
            border-left: none;
            padding: 11px 16px;
        }

        /* BOTÓN DE REGISTRO */
        .btn-register {
            width: 100%;
            padding: 8px 16px;
            background: linear-gradient(180deg, #0a66c2 0%, #0952aa 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            transition: .2s;
        }

        .btn-register:hover {
            background: linear-gradient(180deg, #0952aa 0%, #084299 100%);
        }

        /* BOTÓN MOSTRAR CONTRASEÑA */
        .toggle-password {
            background-color: #f5f6f7;
            border: 1px solid #ced0d4;
            color: #0a66c2;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
        }

        .toggle-password:hover {
            background-color: #ebedf0;
        }

        .helper-text {
            font-size: 12px;
            color: #65676b;
            margin-top: 4px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .register-wrapper {
                flex-direction: column;
                text-align: center;
            }

            .facebook-logo {
                font-size: 42px;
            }

            .facebook-tagline {
                font-size: 20px;
            }

            .register-form-wrapper {
                max-width: 400px;
            }
        }
    </style>
</head>

<body>

<div class="register-container">
    <div class="register-wrapper">

        <!-- Marca -->
        <div class="register-brand">
            <div class="facebook-logo">Acceso</div>
            <div class="facebook-tagline">Crea tu cuenta ahora</div>
        </div>

        <!-- Formulario -->
        <div class="register-form-wrapper">

            <div class="register-card">

                <?php displayMessage(); ?>

                <form method="POST" action="" novalidate>

                    <!-- NOMBRE -->
                    <div class="form-group">
                        <input type="text"
                               class="form-control"
                               name="nombre"
                               value="<?= htmlspecialchars($form['nombre'] ?? '') ?>"
                               placeholder="Nombre Completo"
                               maxlength="100"
                               required>
                    </div>

                    <!-- CORREO -->
                    <div class="form-group">
                        <input type="email"
                               class="form-control"
                               name="correo"
                               value="<?= htmlspecialchars($form['correo'] ?? '') ?>"
                               placeholder="Correo Electrónico"
                               maxlength="150"
                               required>
                    </div>

                    <!-- CONTRASEÑA -->
                    <div class="form-group">
                        <div class="input-group">
                            <input type="password"
                                   id="pass1"
                                   class="form-control"
                                   name="contraseña"
                                   placeholder="Contraseña (mín. 6 caracteres)"
                                   minlength="6"
                                   required>

                            <button type="button"
                                    class="btn toggle-password"
                                    onclick="togglePassword('pass1','text1')">
                                <span id="text1">Ver</span>
                            </button>
                        </div>
                        <div class="helper-text">Mínimo 6 caracteres</div>
                    </div>

                    <!-- CONFIRMAR CONTRASEÑA -->
                    <div class="form-group">
                        <div class="input-group">
                            <input type="password"
                                   id="pass2"
                                   class="form-control"
                                   name="confirmar_contraseña"
                                   placeholder="Confirmar Contraseña"
                                   required>

                            <button type="button"
                                    class="btn toggle-password"
                                    onclick="togglePassword('pass2','text2')">
                                <span id="text2">Ver</span>
                            </button>
                        </div>
                    </div>

                    <!-- CAPTCHA -->
                    <div class="form-group mt-2">
                        <?php $math_question = generateMathCaptcha(); ?>

                        <label class="fw-bold" style="font-size:13px;">
                            Verificación:
                        </label>

                        <input type="text"
                               name="math_captcha"
                               class="form-control"
                               placeholder="Responde: <?= $math_question ?>">
                    </div>

                    <!-- BOTÓN -->
                    <button type="submit" class="btn-register">Crear Cuenta</button>

                </form>
            </div>

            <!-- LINK A LOGIN -->
            <div class="register-card text-center">
                <p style="margin:0;color:#65676b;">
                    ¿Ya tienes cuenta?
                    <a href="login.php" style="color:#0a66c2;font-weight:600;text-decoration:none;">
                        Inicia sesión
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>

<script>
function togglePassword(fieldId, textId) {
    const input = document.getElementById(fieldId);
    const text = document.getElementById(textId);

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
