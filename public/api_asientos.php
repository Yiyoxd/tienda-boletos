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

require_once __DIR__ . "/../app/models/Asiento.php";
require_once __DIR__ . "/../app/controllers/AsientoController.php";
require_once __DIR__ . "/../lib/functions.php";
require_once __DIR__ . "/../lib/LimpiarDatos.php";

header("Content-Type: application/json");

$data   = json_decode(file_get_contents("php://input"), true);
$accion = $_GET["accion"] ?? "";

try {
    $controller = new AsientoController();

    switch ($accion) {
        case "listar":
        case "disponibles":
        case "ocupados":
            $sala_funcion_id = (int)($data["sala_funcion_id"] ?? -1);
            if ($sala_funcion_id < 0) {
                Functions::respuesta(400, false, "ID de sala_función inválido");
            }
            // opcional: verificar existencia en DB
            $existe = Asiento::obtenerPorSalaFuncion($sala_funcion_id);
            if (empty($existe) && $accion === 'listar') {
                Functions::respuesta(404, false, "Sala-Función no encontrada o sin asientos");
                return;
            }

            if ($accion === "listar") {
                $controller->listar($sala_funcion_id);
            } elseif ($accion === "disponibles") {
                $controller->listarPorEstado($sala_funcion_id, 'libre');
            } else { // ocupados
                $controller->listarPorEstado($sala_funcion_id, 'reservado');
            }
            break;

        case "cambiar_estado":
            $asiento_id = (int)($data["id"] ?? 0);
            if ($asiento_id <= 0) {
                Functions::respuesta(400, false, "ID de asiento inválido");
                return;
            }
            $estado = LimpiarDatos::normalizarTexto($data["estado"] ?? '');
            if (!in_array($estado, ['libre', 'reservado'])) {
                Functions::respuesta(400, false, "Estado inválido");
                return;
            }
            $controller->cambiarEstado($asiento_id, $estado);
            break;

        case "cambiar_estado_coords":
            $sala_funcion_id = (int)($data["sala_funcion_id"] ?? 0);
            $fila            = LimpiarDatos::normalizarTextoMayus($data["fila"] ?? '');
            $numero          = (int)($data["numero"] ?? 0);
            $estado          = LimpiarDatos::normalizarTexto($data["estado"] ?? '');

            if ($sala_funcion_id <= 0) {
                Functions::respuesta(400, false, "ID de sala_función inválido");
                return;
            }
            if (!preg_match('/^[A-J]$/', $fila)) {
                Functions::respuesta(400, false, "Fila inválida (A–J)");
                return;
            }
            if ($numero < 1 || $numero > 20) {
                Functions::respuesta(400, false, "Número inválido (1–20)");
                return;
            }
            if (!in_array($estado, ['libre', 'reservado'])) {
                Functions::respuesta(400, false, "Estado inválido");
                return;
            }

            $controller->cambiarEstadoPorCoordenadas($sala_funcion_id, $fila, $numero, $estado);
            break;

        case "obtener_estado":
            $sala_funcion_id = (int)($data["sala_funcion_id"] ?? 0);
            $fila            = LimpiarDatos::normalizarTextoMayus($data["fila"] ?? '');
            $numero          = (int)($data["numero"] ?? 0);

            if ($sala_funcion_id <= 0) {
                Functions::respuesta(400, false, "ID de sala_función inválido");
                return;
            }
            if (!preg_match('/^[A-J]$/', $fila)) {
                Functions::respuesta(400, false, "Fila inválida (A–J)".$fila);
                return;
            }
            if ($numero < 1 || $numero > 20) {
                Functions::respuesta(400, false, "Número inválido (1–20)");
                return;
            }

            $controller->obtenerEstado($sala_funcion_id, $fila, $numero);
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
