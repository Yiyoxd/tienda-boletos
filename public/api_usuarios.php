<?php
/* =========================================================
   API: api_usuarios.php
   Maneja registro, login, info de sesión y logout
   ========================================================= */

/* ---------- Mostrar y loguear errores brutalmente ---------- */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');
error_reporting(E_ALL);

set_exception_handler(function ($e) {
    error_log("[EXCEPTION] {$e->getMessage()} en {$e->getFile()}:{$e->getLine()}");
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error del servidor']);
    exit;
});
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("[ERROR] $errstr en $errfile:$errline");
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error del sistema']);
    exit;
});

/* ---------- Sesión (1 semana) ---------- */
session_set_cookie_params([
    'lifetime' => 60 * 60 * 24 * 7,   // 1 semana
    'path'     => '/',
    'secure'   => false,              // true si tienes HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

/* ---------- Cargas ---------- */
require_once __DIR__ . '/../app/models/Usuario.php';
require_once __DIR__ . '/../app/controllers/UsuarioController.php';
require_once __DIR__ . '/../lib/LimpiarDatos.php';
require_once __DIR__ . '/../lib/functions.php';

header('Content-Type: application/json');

/* ---------- Dispatcher ---------- */
$accion = $_GET['accion'] ?? '';

try {
    switch ($accion) {

        /* ======== REGISTRAR ======== */
        case 'registrar': {
            $data       = json_decode(file_get_contents('php://input'), true);
            $nombre     = LimpiarDatos::normalizarTextoCapital($data['nombre']  ?? '');
            $correo     = LimpiarDatos::normalizarTexto($data['correo']  ?? '');
            $contrasena = $data['password'] ?? '';

            $usuario    = new Usuario($nombre, $correo, $contrasena);
            $controller = new UsuarioController();
            $controller->registrar($usuario);
            break;
        }

        /* ======== LOGIN ======== */
        case 'login': {
            $data       = json_decode(file_get_contents('php://input'), true);
            $correo     = LimpiarDatos::normalizarTexto($data['correo']  ?? '');
            $contrasena = $data['password'] ?? '';

            $usuario    = new Usuario('', $correo, $contrasena);  // nombre no necesario para login
            $controller = new UsuarioController();
            $controller->login($usuario);
            break;
        }

        /* ======== INFO SESIÓN ======== */
        case 'info': {
            if (isset($_SESSION['usuario_id'])) {
                Functions::respuesta(200, true, 'Logueado', [
                    'usuario_id' => $_SESSION['usuario_id'],
                    'nombre'     => $_SESSION['nombre']
                ]);
            } else {
                Functions::respuesta(200, false, 'Anónimo');
            }
            break;
        }

        /* ======== LOGOUT ======== */
        case 'logout': {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $p = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $p['path'], $p['domain'], $p['secure'], $p['httponly']);
            }
            session_destroy();
            Functions::respuesta(200, true, 'Sesión cerrada');
            break;
        }

        /* ======== ACCIÓN DESCONOCIDA ======== */
        default:
            Functions::accionInvalida();   // envía 400 y exit
    }

} catch (Throwable $e) {
    error_log("[CATCH] {$e->getMessage()}");
    Functions::respuesta(400, false, $e->getMessage());
}
