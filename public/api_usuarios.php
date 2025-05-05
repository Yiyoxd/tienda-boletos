<?php

// Mostrar y loggear errores de forma brutal
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . '/../logs/error.log'); // Asegúrate de que exista la carpeta logs/
error_reporting(E_ALL);

// Handlers para errores fatales
set_exception_handler(function($e) {
    error_log("[EXCEPTION] " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error del servidor",
        "error" => $e->getMessage()
    ]);
    exit;
});

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("[ERROR] $errstr en $errfile:$errline");
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error del sistema",
        "error"   => $errstr
    ]);
    exit;
});


require_once __DIR__ . "/../app/models/Usuario.php";
require_once __DIR__ . "/../lib/functions.php";
require_once __DIR__ . "/../app/controllers/UsuarioController.php";
require_once __DIR__ . "/../lib/LimpiarDatos.php";

header("Content-Type: application/json");

// Leer datos del cuerpo de la petición
$data = json_decode(file_get_contents("php://input"), true);
$accion = $_GET["accion"] ?? "";

try {
    $nombre     = LimpiarDatos::normalizarTexto($data["nombre"] ?? "");
    $correo     = LimpiarDatos::normalizarTexto($data["correo"] ?? "");
    $contrasena = $data["password"] ?? "";

    $usuario = new Usuario($nombre, $correo, $contrasena);
    $controller = new UsuarioController();

    switch ($accion) {
        case "login":
            $controller->login($usuario);
            break;

        case "registrar":
            $controller->registrar($usuario);
            break;

        default:
            Functions::accionInvalida();
            break;
    }

    exit;

} catch (Exception $e) {
    error_log("[CATCH BLOCK] " . $e->getMessage());
    Functions::respuesta(400, false, $e->getMessage());
    exit;
}
