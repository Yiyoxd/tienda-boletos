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

require_once __DIR__ . "/../app/models/SalaFuncion.php";
require_once __DIR__ . "/../lib/functions.php";
require_once __DIR__ . "/../app/controllers/SalaFuncionController.php";
require_once __DIR__ . "/../lib/LimpiarDatos.php";

header("Content-Type: application/json");

$data   = json_decode(file_get_contents("php://input"), true);
$accion = $_GET["accion"] ?? "";

try {
    $controller = new SalaFuncionController();

    switch ($accion) {
        case "registrar":
            $sala_id    = (int)($data["sala_id"] ?? 0);
            $funcion_id = (int)($data["funcion_id"] ?? 0);
            $controller->registrar($sala_id, $funcion_id);
            break;

        case "obtener":
            $id = (int)($data["id"] ?? 0);
            $controller->obtener($id);
            break;

        case "listar":
            $controller->listar();
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
