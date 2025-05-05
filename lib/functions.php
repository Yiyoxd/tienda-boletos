<?php

class Functions {
    public static function respuesta($codigo, $succes, $mensaje, $data = null) {
        http_response_code(200);
        echo json_encode([
            "succes" => $succes,
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