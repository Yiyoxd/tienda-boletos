<?php
require_once __DIR__ ."/../../core/Conexion.php";
require_once __DIR__ ."/../../core/Consultas.php";
require_once __DIR__ ."/../../app/models/Asiento.php";

class SalaFuncion {

    public static function registrar($sala_id, $funcion_id) {
        $stmt = Consultas::ejecutar(
            "INSERT INTO sala_funcion (sala_id, funcion_id) VALUES (?, ?)",
            "ii",
            [$sala_id, $funcion_id]
        );
    
        if ($stmt->affected_rows === 1) {
            $idSalaFuncion = $stmt->insert_id;
            $stmt->close();
            Asiento::generarAsientos($idSalaFuncion);
            return true;
        }
    
        $stmt->close();
        return false;
    }

    public static function getPorId($id) {
        $stmt = Consultas::ejecutar(
            "SELECT id, sala_id, funcion_id FROM sala_funcion WHERE id = ?",
            "i",
            [$id]
        );

        if ($stmt->num_rows === 0) {
            $stmt->free_result();
            $stmt->close();
            return null;
        }

        $stmt->bind_result($id, $sala_id, $funcion_id);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        return [
            "id"         => $id,
            "sala_id"    => $sala_id,
            "funcion_id" => $funcion_id
        ];
    }

    public static function obtenerTodas() {
        $stmt = Consultas::ejecutar(
            "SELECT id, sala_id, funcion_id FROM sala_funcion ORDER BY id DESC"
        );

        $resultado = [];
        $stmt->bind_result($id, $sala_id, $funcion_id);
        while ($stmt->fetch()) {
            $resultado[] = [
                "id"         => $id,
                "sala_id"    => $sala_id,
                "funcion_id" => $funcion_id
            ];
        }
        $stmt->close();
        return $resultado;
    }

    public static function yaExiste($sala_id, $funcion_id) {
        $stmt = Consultas::ejecutar(
            "SELECT 1 FROM sala_funcion WHERE sala_id = ? AND funcion_id = ?",
            "ii",
            [$sala_id, $funcion_id]
        );
    
        $existe = ($stmt->num_rows > 0);
        $stmt->free_result();
        $stmt->close();
        return $existe;
    }
    
}
