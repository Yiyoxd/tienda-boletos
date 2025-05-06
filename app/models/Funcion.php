<?php
require_once __DIR__ ."/../../core/Conexion.php";
require_once __DIR__ ."/../../core/Consultas.php";

class Funcion {

    public static function registrar($pelicula_id, $fecha, $hora, $precio) {
        $stmt = Consultas::ejecutar(
            "INSERT INTO funciones (pelicula_id, fecha, hora, precio) VALUES (?, ?, ?, ?)",
            "issd",
            [$pelicula_id, $fecha, $hora, $precio]
        );

        $ok = ($stmt->affected_rows === 1);
        $stmt->close();
        return $ok;
    }

    public static function obtenerPorId($id) {
        $stmt = Consultas::ejecutar(
            "SELECT id, pelicula_id, fecha, hora, precio FROM funciones WHERE id = ?",
            "i",
            [$id]
        );
    
        if ($stmt->num_rows === 0) {
            $stmt->free_result();
            $stmt->close();
            return [];
        }
    
        $stmt->bind_result($id, $pelicula_id, $fecha, $hora, $precio);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();
    
        return [
            'id'          => $id,
            'pelicula_id' => $pelicula_id,
            'fecha'       => $fecha,
            'hora'        => $hora,
            'precio'      => $precio
        ];
    }

    public static function obtenerPorPelicula($pelicula_id) {
        $stmt = Consultas::ejecutar(
            "SELECT id, pelicula_id, fecha, hora, precio FROM funciones WHERE pelicula_id = ? ORDER BY fecha, hora",
            "i",
            [$pelicula_id]
        );

        $resultado = [];
        $stmt->bind_result($id, $pid, $fecha, $hora, $precio);

        while ($stmt->fetch()) {
            $resultado[] = [
                'id'          => $id,
                'pelicula_id' => $pid,
                'fecha'       => $fecha,
                'hora'        => $hora,
                'precio'      => $precio
            ];
        }

        $stmt->close();
        return $resultado;
    }

    public static function obtenerTodas() {
        $stmt = Consultas::ejecutar(
            "SELECT id, pelicula_id, fecha, hora, precio FROM funciones ORDER BY fecha, hora"
        );

        $resultado = [];
        $stmt->bind_result($id, $pid, $fecha, $hora, $precio);

        while ($stmt->fetch()) {
            $resultado[] = [
                'id'          => $id,
                'pelicula_id' => $pid,
                'fecha'       => $fecha,
                'hora'        => $hora,
                'precio'      => $precio
            ];
        }

        $stmt->close();
        return $resultado;
    }
}
