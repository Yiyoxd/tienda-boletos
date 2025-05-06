<?php
require_once __DIR__ . "/../models/Asiento.php";
require_once __DIR__ . "/../../lib/functions.php";

class AsientoController {
    public function generar($sala_funcion_id) {
        try {
            $creados = Asiento::generarAsientos($sala_funcion_id);
            Functions::respuesta(200, true, "Asientos generados", $creados);
        } catch (Exception $e) {
            Functions::respuesta(500, false, "Error al generar asientos", ["exception" => $e->getMessage()]);
        }
    }

    public function listar($sala_funcion_id) {
        try {
            $asientos = Asiento::obtenerPorSalaFuncion($sala_funcion_id);
            Functions::respuesta(200, true, "Asientos encontrados", $asientos);
        } catch (Exception $e) {
            Functions::respuesta(500, false, "Error al listar asientos", ["exception" => $e->getMessage()]);
        }
    }

    public function listarPorEstado($sala_funcion_id, $estado) {
        try {
            $asientos = Asiento::obtenerPorEstado($sala_funcion_id, $estado);
            Functions::respuesta(200, true, "Asientos filtrados", $asientos);
        } catch (Exception $e) {
            Functions::respuesta(500, false, "Error al filtrar asientos", ["exception" => $e->getMessage()]);
        }
    }

    public function cambiarEstado($id_asiento, $nuevo_estado) {
        try {
            $ok = Asiento::cambiarEstado($id_asiento, $nuevo_estado);
            $mensaje = $ok ? "Estado actualizado" : "No se pudo actualizar";
            Functions::respuesta($ok ? 200 : 400, $ok, $mensaje);
        } catch (Exception $e) {
            Functions::respuesta(500, false, "Error al cambiar estado", ["exception" => $e->getMessage()]);
        }
    }

    /**
     * Cambia el estado usando sala_funcion + fila + número
     */
    public function cambiarEstadoPorCoordenadas($sala_funcion_id, $fila, $numero, $nuevo_estado) {
        try {
            $ok = Asiento::cambiarEstadoPorCoordenadas($sala_funcion_id, $fila, $numero, $nuevo_estado);
            $mensaje = $ok ? "Estado actualizado" : "No se pudo actualizar";
            Functions::respuesta($ok ? 200 : 400, $ok, $mensaje);
        } catch (Exception $e) {
            Functions::respuesta(500, false, "Error al cambiar estado por coordenadas", ["exception" => $e->getMessage()]);
        }
    }

    /**
     * Obtiene el estado ('libre'|'reservado') de un asiento por sala_funcion + fila + número
     */
    public function obtenerEstado($sala_funcion_id, $fila, $numero) {
        try {
            $estado = Asiento::obtenerEstado($sala_funcion_id, $fila, $numero);
            if ($estado !== null) {
                Functions::respuesta(200, true, "Estado encontrado", ["estado" => $estado]);
            } else {
                Functions::respuesta(404, false, "Asiento no encontrado");
            }
        } catch (Exception $e) {
            Functions::respuesta(500, false, "Error al obtener estado", ["exception" => $e->getMessage()]);
        }
    }
}
