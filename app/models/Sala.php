<?php
require_once __DIR__ ."/../../core/Conexion.php";
require_once __DIR__ ."/../../core/Consultas.php";

class Sala {

    public static function registrar($nombre) {
        $stmt = Consultas::ejecutar(
            "INSERT INTO salas(nombre) VALUES (?)",
            "s",
            [$nombre]
        );

        $ok = ($stmt->affected_rows === 1);
        $stmt->close();
        return $ok;
    }

    public static function getSala($id) {
        $stmt = Consultas::ejecutar (
            "SELECT id, nombre 
             FROM salas WHERE id = ?",
            "i",
            [$id]
        );

        if ($stmt->num_rows === 0) {
            $stmt->free_result();
            $stmt->close();
            return [];
        }

        $stmt->bind_result($id, $nombre);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        return [
            'id'            => $id,
            'nombre'        => $nombre
        ];
    }

    public static function obtenerTodas() {
        $stmt = Consultas::ejecutar(
            "SELECT id, nombre FROM salas ORDER BY id DESC"
        );

        $resultado = [];
        $stmt->bind_result($id, $nombre);
    
        while ($stmt->fetch()) {
            $resultado[] = [
                'id'            => $id,
                'nombre'        => $nombre
            ];
        }
    
        $stmt->close();
        return $resultado;
    }
}
