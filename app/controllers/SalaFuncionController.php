<?php
require_once __DIR__ ."/../models/SalaFuncion.php";
require_once __DIR__ ."/../../lib/functions.php";

class SalaFuncionController {
    public function registrar($sala_id, $funcion_id) {
        try {
            if (SalaFuncion::yaExiste($sala_id, $funcion_id)) {
                Functions::respuesta(409, false, "La sala ya está asignada a esta función");
                return;
            }
            
            $ok = SalaFuncion::registrar($sala_id, $funcion_id);
            if ($ok) {
                Functions::respuesta(200, true, "Sala-Función registrada correctamente");
            } else {
                Functions::respuesta(500, false, "Error al registrar Sala-Función");
            }
        } catch (Exception $e) {
            Functions::respuesta(500, false, "Error Interno", ["exception" => $e->getMessage()]);
        }
    }

    public function obtener($id) {
        try {
            $sf = SalaFuncion::getPorId($id);
            if ($sf) {
                Functions::respuesta(200, true, "Sala-Función encontrada", $sf);
            } else {
                Functions::respuesta(404, false, "Sala-Función no encontrada");
            }
        } catch (Exception $e) {
            Functions::respuesta(500, false, "Error Interno", ["exception" => $e->getMessage()]);
        }
    }

    public function listar() {
        try {
            $result = SalaFuncion::obtenerTodas();
            Functions::respuesta(200, true, "Sala-Funciones obtenidas", $result);
        } catch (Exception $e) {
            Functions::respuesta(500, false, "Error Interno", ["exception" => $e->getMessage()]);
        }
    }
}
