<?php

require_once __DIR__ ."/../models/Pelicula.php";
require_once __DIR__ ."/../../lib/functions.php";

class PeliculaController {

    public function registrar($titulo, $descripcion, $duracion, $clasificacion, $imagen, $estado = "activa") {
        try {
            $ok = Pelicula::registrar($titulo, $descripcion, $duracion, $clasificacion, $imagen, $estado);
            if ($ok) {
                Functions::respuesta(200, true, "Película registrada correctamente");
            } else {
                Functions::respuesta(500, false, "Error al registrar la película");
            }
        } catch (Exception $e) {
            Functions::respuesta (
                500,
                false,
                "Error Interno",
                ["exception" => $e->getMessage()]
            );
        }
    }

    public function obtener($id) {
        try {
            $pelicula = Pelicula::getPelicula($id);
            if ($pelicula) {
                Functions::respuesta(200, true, "Película encontrada", $pelicula);
            } else {
                Functions::respuesta(404, false, "Película no encontrada");
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

    public function obtenerTodas() {
        try {
            $peliculas = Pelicula::obtenerTodas();
            Functions::respuesta(200, true, "Películas obtenidas", $peliculas);
        } catch (Exception $e) {
            Functions::respuesta (
                500,
                false,
                "Error Interno",
                ["exception" => $e->getMessage()]
            );
        }
    }
}
