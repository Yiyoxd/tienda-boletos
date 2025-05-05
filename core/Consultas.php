<?php
require_once "Conexion.php";

class Consultas {
    public static function ejecutar($sql, $tipos, $params) {
        $db = Conexion::getConexion();
        $consulta = $db->prepare($sql);

        if (!$consulta) {
            throw new Exception("Error en la consulta" .$db->error);
        }

        $consulta->execute($params);
        return $consulta;
    }
}