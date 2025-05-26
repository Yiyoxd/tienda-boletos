<?php
ini_set("display_errors",1); error_reporting(E_ALL);
header("Content-Type: application/json");

require_once __DIR__ . "/../core/Conexion.php";
require_once __DIR__ . "/../core/Consultas.php";
require_once __DIR__ . "/../app/models/Asiento.php";
require_once __DIR__ . "/../app/models/Reserva.php";
require_once __DIR__ . "/../lib/functions.php";

$data   = json_decode(file_get_contents("php://input"), true);
$accion = $_GET['accion'] ?? '';

switch ($accion) {
    case 'crear':
        $sala_funcion_id = intval($data["sala_funcion_id"] ?? 0);
        $asientos        = $data["asientos"] ?? [];

        if ($sala_funcion_id <= 0 || !is_array($asientos) || count($asientos) === 0) {
            Functions::respuesta(400, false, "Datos incompletos");
        }

        $reserva_id = Reserva::crear($sala_funcion_id, $asientos);
        if ($reserva_id) {
            Functions::respuesta(200, true, "Reserva exitosa", ["reserva_id" => $reserva_id]);
        } else {
            Functions::respuesta(500, false, "No se pudo guardar la reserva");
        }
        break;

    default:
        Functions::respuesta(400, false, "Acción inválida");
}
