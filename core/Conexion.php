<?php

require_once __DIR__ ."/../config/config.php";

class Conexion {
    public static function getConexion() {
        return new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }
}