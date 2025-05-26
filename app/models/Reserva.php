<?php
require_once __DIR__ . "/../../core/Conexion.php";
require_once __DIR__ . "/../../core/Consultas.php";

class Reserva {

    /**
     * @param int        $sala_funcion_id
     * @param array      $asientos  [['fila'=>'A','numero'=>1], …]
     * @param int|null   $usuario_id
     * @return int|false id de la reserva o false si algo falla
     */
    public static function crear($sala_funcion_id, array $asientos, $usuario_id = null) {

        $cx = Conexion::getConexion();
        $cx->begin_transaction();

       /* 1.- Obtener funcion_id y precio de la función */
        $q = Consultas::ejecutar(
            "SELECT f.id, f.precio
            FROM sala_funcion sf
            JOIN funciones f ON sf.funcion_id = f.id
            WHERE sf.id = ? LOCK IN SHARE MODE",
            "i",
            [$sala_funcion_id]
        );

        $q->bind_result($funcion_id, $precio_unit);
        if (!$q->fetch()) { $cx->rollback(); return false; }
        $q->close();

        $total    = $precio_unit * count($asientos);
        $estado   = 'pendiente';         // o 'pagado' si ya cobraste

        /* 2.- Insertar encabezado de reserva */
        if ($usuario_id !== null) {
            $enc = Consultas::ejecutar(
                "INSERT INTO reservas (usuario_id, funcion_id, total, estado)
                 VALUES (?, ?, ?, ?)",
                "iids",
                [$usuario_id, $funcion_id, $total, $estado]
            );
        } else {
            $enc = Consultas::ejecutar(
                "INSERT INTO reservas (funcion_id, total, estado)
                 VALUES (?, ?, ?)",
                "ids",
                [$funcion_id, $total, $estado]
            );
        }

        if ($enc->affected_rows !== 1) { $cx->rollback(); return false; }
        $reserva_id = $enc->insert_id;
        $enc->close();

        /* 3.- Por cada asiento → bloquear + asociar */
        foreach ($asientos as $a) {
            $fila = $a['fila'];  $num = $a['numero'];

            // 3-A. Traer asiento y bloquear
            $sel = Consultas::ejecutar(
                "SELECT id, estado
                   FROM asientos
                  WHERE sala_funcion_id = ? AND fila = ? AND numero = ?
                  FOR UPDATE",
                "isi", [$sala_funcion_id, $fila, $num]
            );
            $sel->bind_result($asiento_id, $estado);
            if (!$sel->fetch() || $estado !== 'libre') { $cx->rollback(); return false; }
            $sel->close();

            // 3-B. Marcar como reservado
            $up = Consultas::ejecutar(
                "UPDATE asientos SET estado = 'reservado' WHERE id = ?",
                "i", [$asiento_id]
            );
            if ($up->affected_rows !== 1) { $cx->rollback(); return false; }
            $up->close();

            // 3-C. Insertar detalle
            $det = Consultas::ejecutar(
                "INSERT INTO reserva_asientos (reserva_id, asiento_id)
                 VALUES (?, ?)",
                "ii", [$reserva_id, $asiento_id]
            );
            if ($det->affected_rows !== 1) { $cx->rollback(); return false; }
            $det->close();
        }

        $cx->commit();
        return $reserva_id;
    }
}
