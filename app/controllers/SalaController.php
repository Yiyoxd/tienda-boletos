<?php

require_once __DIR__ ."/../models/Sala.php";
require_once __DIR__ ."/../../lib/functions.php";
require_once __DIR__ ."/../../lib/LimpiarDatos.php";

class SalaController {

    public function registrar($nombre) {
        try {
            $nombre = LimpiarDatos::normalizarTextoCapital($nombre);
            if (empty($nombre)) {
                Functions::respuesta(400, false, "El nombre de la sala no puede estar vacío");
                return;
            }

            $ok = Sala::registrar($nombre);
            if ($ok) {
                Functions::respuesta(200, true, "Sala registrada correctamente");
            } else {
                Functions::respuesta(500, false, "Error al registrar la sala");
            }
        } catch (Exception $e) {
            Functions::respuesta(500, false, "Error Interno", [
                "exception" => $e->getMessage()
            ]);
        }
    }

    public function obtener($id) {
        try {
            $sala = Sala::getSala($id);
            if ($sala) {
                Functions::respuesta(200, true, "Sala encontrada", $sala);
            } else {
                Functions::respuesta(404, false, "Sala no encontrada");
            }
        } catch (Exception $e) {
            Functions::respuesta(500, false, "Error Interno", [
                "exception" => $e->getMessage()
            ]);
        }
    }

    public function obtenerTodas() {
        try {
            $salas = Sala::obtenerTodas();
            Functions::respuesta(200, true, "Salas obtenidas", $salas);
        } catch (Exception $e) {
            Functions::respuesta(500, false, "Error Interno", [
                "exception" => $e->getMessage()
            ]);
        }
    }
}
