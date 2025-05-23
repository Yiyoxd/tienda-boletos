<?php

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . '/../logs/error.log');
error_reporting(E_ALL);

set_exception_handler(function($e) {
    error_log("[EXCEPTION] " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error del servidor",
        "error"   => $e->getMessage()
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

require_once __DIR__ . "/../app/models/Funcion.php";
require_once __DIR__ . "/../lib/functions.php";
require_once __DIR__ . "/../app/controllers/FuncionController.php";
require_once __DIR__ . "/../lib/LimpiarDatos.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$accion = $_GET["accion"] ?? "";

try {
    $controller = new FuncionController();

    switch ($accion) {
        case "registrar":
            $pelicula_id = (int)($data["pelicula_id"] ?? 0);
            $fecha       = $data["fecha"] ?? "";
            $hora        = $data["hora"] ?? "";
            $precio      = (float)($data["precio"] ?? 0);
            
            $controller->registrar($pelicula_id, $fecha, $hora, $precio);
            break;

        case "obtener":
            $id = (int)($data["id"] ?? 0);
            $controller->obtener($id);
            break;

        case "por_pelicula":
            $pelicula_id = (int)($data["pelicula_id"] ?? 0);
            $controller->obtenerPorPelicula($pelicula_id);
            break;

        case "listar":
            $controller->obtenerTodas();
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