<?php
require_once __DIR__ . "/../models/Reserva.php";
require_once __DIR__ . "/../../lib/functions.php";
require_once __DIR__ . "/../../lib/LimpiarDatos.php";

class ReservaController {

    public function crear(array $payload) {
        $sala_funcion_id = (int)($payload['sala_funcion_id'] ?? 0);
        $asientos        = $payload['asientos'] ?? [];

        if ($sala_funcion_id <= 0 || empty($asientos)) {
            Functions::respuesta(400, false, "Datos incompletos");
            return;
        }

        // validar rango de asientos
        foreach ($asientos as $a) {
            $fila = LimpiarDatos::normalizarTextoMayus($a['fila'] ?? '');
            $num  = (int)($a['numero'] ?? 0);
            if (!preg_match('/^[A-J]$/', $fila) || $num < 1 || $num > 20) {
                Functions::respuesta(400, false, "Asiento inválido: {$fila}{$num}");
                return;
            }
        }

        $usuario_id = null;   // TODO: obtener si manejas login

        $id = Reserva::crear($sala_funcion_id, $asientos, $usuario_id);
        if ($id === false) {
            Functions::respuesta(409, false, "Alguno de los asientos ya fue ocupado");
        } else {
            Functions::respuesta(201, true, "Reserva creada", ["reserva_id"=>$id]);
        }
    }
}
