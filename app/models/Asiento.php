<?php
require_once __DIR__ . "/../../core/Conexion.php";
require_once __DIR__ . "/../../core/Consultas.php";

class Asiento {
    public static function registrar($sala_funcion_id, $fila, $numero, $estado = 'libre') {
        $stmt = Consultas::ejecutar(
            "INSERT INTO asientos (sala_funcion_id, fila, numero, estado) VALUES (?, ?, ?, ?)",
            "isis",
            [$sala_funcion_id, $fila, $numero, $estado]
        );
    
        $ok = ($stmt->affected_rows === 1);
        $stmt->close();
        return $ok;
    }

    public static function generarAsientos($sala_funcion_id) {
        $filas = range('A', 'J');
        $resultado = [];

        foreach ($filas as $fila) {
            for ($i = 1; $i <= 20; $i++) {
                $ok = self::registrar($sala_funcion_id, $fila, $i, 'libre');
                if ($ok) {
                    $resultado[] = "$fila$i";
                }
            }
        }
        
        return $resultado;
    }

    public static function obtenerPorSalaFuncion($sala_funcion_id) {
        $stmt = Consultas::ejecutar(
            "SELECT id, fila, numero, estado FROM asientos WHERE sala_funcion_id = ? ORDER BY fila, numero",
            "i",
            [$sala_funcion_id]
        );

        $resultado = [];
        $stmt->bind_result($id, $fila, $numero, $estado);

        while ($stmt->fetch()) {
            $resultado[] = compact("id", "fila", "numero", "estado");
        }

        $stmt->close();
        return $resultado;
    }

    public static function obtenerPorEstado($sala_funcion_id, $estado) {
        $stmt = Consultas::ejecutar(
            "SELECT id, fila, numero FROM asientos WHERE sala_funcion_id = ? AND estado = ? ORDER BY fila, numero",
            "is",
            [$sala_funcion_id, $estado]
        );

        $resultado = [];
        $stmt->bind_result($id, $fila, $numero);

        while ($stmt->fetch()) {
            $resultado[] = compact("id", "fila", "numero");
        }

        $stmt->close();
        return $resultado;
    }

    public static function cambiarEstado($id_asiento, $nuevo_estado) {
        $stmt = Consultas::ejecutar(
            "UPDATE asientos SET estado = ? WHERE id = ?",
            "si",
            [$nuevo_estado, $id_asiento]
        );

        $ok = ($stmt->affected_rows === 1);
        $stmt->close();
        return $ok;
    }
    
    /**
     * Cambia el estado de un asiento por sala_funcion, fila y número.
     *
     * @param int    $sala_funcion_id
     * @param string $fila
     * @param int    $numero
     * @param string $nuevo_estado
     * @return bool
     */
    public static function cambiarEstadoPorCoordenadas($sala_funcion_id, $fila, $numero, $nuevo_estado) {
        $stmt = Consultas::ejecutar(
            "UPDATE asientos
                SET estado = ?
                WHERE sala_funcion_id = ?
                AND fila = ?
                AND numero = ?",
            "sisi",  // estado:string, sala_funcion_id:int, fila:string, numero:int
            [$nuevo_estado, $sala_funcion_id, $fila, $numero]
        );

        $ok = ($stmt->affected_rows === 1);
        $stmt->close();
        return $ok;
    }

    /**
     * Devuelve el estado ('libre' o 'reservado') de un asiento.
     *
     * @param int    $sala_funcion_id
     * @param string $fila
     * @param int    $numero
     * @return string|null  Estado o null si no existe
     */
    public static function obtenerEstado($sala_funcion_id, $fila, $numero) {
        $stmt = Consultas::ejecutar(
            "SELECT estado
                FROM asientos
                WHERE sala_funcion_id = ?
                AND fila = ?
                AND numero = ?",
            "isi",
            [$sala_funcion_id, $fila, $numero]
        );

        $estado = null;
        $stmt->bind_result($estado);
        if (!$stmt->fetch()) {
            $estado = null;
        }

        $stmt->close();
        return $estado;
    }
}

