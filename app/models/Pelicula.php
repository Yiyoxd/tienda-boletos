<?php
require_once __DIR__ ."/../../core/Conexion.php";
require_once __DIR__ ."/../../core/Consultas.php";

class Pelicula {
    public static function registrar($titulo, $descripcion, $duracion, $clasificacion, $imagen, $estado = "activa") {
        $stmt = Consultas::ejecutar(
            "INSERT INTO peliculas (titulo, descripcion, duracion, clasificacion, imagen, estado)
             VALUES (?, ?, ?, ?, ?, ?)",
            "ssisss",
            [$titulo, $descripcion, $duracion, $clasificacion, $imagen, $estado]
        );

        $ok = ($stmt->affected_rows === 1);
        $stmt->close();
        return $ok;
    }

    public static function getPelicula($id) {
        $stmt = Consultas::ejecutar(
            "SELECT id, titulo, descripcion, duracion, clasificacion, imagen, estado
             FROM peliculas WHERE id = ?",
            "i",
            [$id]
        );

        if ($stmt->num_rows === 0) {
            $stmt->free_result();
            $stmt->close();
            return [];
        }

        $stmt->bind_result($id, $titulo, $descripcion, $duracion, $clasificacion, $imagen, $estado);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        return [
            'id'            => $id,
            'titulo'        => $titulo,
            'descripcion'   => $descripcion,
            'duracion'      => $duracion,
            'clasificacion' => $clasificacion,
            'imagen'        => $imagen,
            'estado'        => $estado
        ];
    }

    public static function obtenerTodas() {
        $stmt = Consultas::ejecutar(
            "SELECT id, titulo, descripcion, duracion, clasificacion, imagen, estado FROM peliculas ORDER BY id DESC"
        );
    
        $resultado = [];
        $stmt->bind_result($id, $titulo, $descripcion, $duracion, $clasificacion, $imagen, $estado);
    
        while ($stmt->fetch()) {
            $resultado[] = [
                'id'            => $id,
                'titulo'        => $titulo,
                'descripcion'   => $descripcion,
                'duracion'      => $duracion,
                'clasificacion' => $clasificacion,
                'imagen'        => $imagen,
                'estado'        => $estado
            ];
        }
    
        $stmt->close();
        return $resultado;
    }
}
