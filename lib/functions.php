<?php

class Functions {
    public static function respuesta($codigo, $success, $mensaje, $data = null) {
        http_response_code($codigo);
        header("Content-Type: application/json");
        echo json_encode([
            "success" => $success,
            "message" => $mensaje,
            "data" => $data
        ]);
    }

    public static function error_interno() {
        self::respuesta(500, false, "Error Interno");
    }

    public static function accionInvalida() {
        self::respuesta(404, false,"Acción Inválida");
    }
}