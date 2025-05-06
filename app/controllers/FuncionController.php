<?php

require_once __DIR__ . "/../models/Funcion.php";
require_once __DIR__ . "/../models/Pelicula.php";
require_once __DIR__ . "/../../lib/functions.php";

class FuncionController {

    public function registrar($pelicula_id, $fecha, $hora, $precio) {
        try {
            $pelicula = Pelicula::getPelicula($pelicula_id);
            if (!$pelicula) {
                Functions::respuesta(404, false, "La película no existe");
                return;
            }
            $ok = Funcion::registrar($pelicula_id, $fecha, $hora, $precio);
            if ($ok) {
                Functions::respuesta(200, true, "Función registrada correctamente");
            } else {
                Functions::respuesta(500, false, "Error al registrar la función");
            }
        } catch (Exception $e) {
            Functions::respuesta(
                500,
                false,
                "Error Interno",
                ["exception" => $e->getMessage()]
            );
        }
    }

    public function obtener($id) {
        try {
            $funcion = Funcion::obtenerPorId($id);
            if ($funcion) {
                Functions::respuesta(200, true, "Función encontrada", $funcion);
            } else {
                Functions::respuesta(404, false, "Función no encontrada");
            }
        } catch (Exception $e) {
            Functions::respuesta(
                500,
                false,
                "Error Interno",
                ["exception" => $e->getMessage()]
            );
        }
    }

    public function obtenerPorPelicula($pelicula_id) {
        try {
            $funciones = Funcion::obtenerPorPelicula($pelicula_id);
            Functions::respuesta(200, true, "Funciones encontradas", $funciones);
        } catch (Exception $e) {
            Functions::respuesta(
                500,
                false,
                "Error Interno",
                ["exception" => $e->getMessage()]
            );
        }
    }

    public function obtenerTodas() {
        try {
            $funciones = Funcion::obtenerTodas();
            Functions::respuesta(200, true, "Funciones obtenidas", $funciones);
        } catch (Exception $e) {
            Functions::respuesta(
                500,
                false,
                "Error Interno",
                ["exception" => $e->getMessage()]
            );
        }
    }
}
