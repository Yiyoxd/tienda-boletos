<?php

class LimpiarDatos {
    public static function minusculas($texto) {
        return strtolower($texto);
    }

    public static function mayusculas($texto) {
        return strtoupper($texto);
    }

    public static function eliminarEspacios($texto):string{
        return preg_replace('/\s+/', '', $texto);
    }

    public static function normalizarTexto($texto) {
        $texto = self::minusculas($texto);
        $texto = self::eliminarEspacios($texto);
        return $texto;
    } 

    public static function normalizarDatos($datos) : array{
        foreach ($datos as $clave => $valor) {
            $datos[$clave] = self::normalizarTexto($valor);
        }
        return $datos;
    }
}