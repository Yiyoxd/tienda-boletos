<?php
require_once __DIR__ ."/Conexion.php";

class Consultas {
    /**
     * Ejecuta una consulta segura y bufferiza el resultado.
     *
     * @param string $sql    La sentencia SQL con placeholders, p. ej. "SELECT * FROM usuarios WHERE email = ?"
     * @param string $tipos  El string de tipos para bind_param, p. ej. "s" o "ss"
     * @param array  $params Los valores a inyectar en los placeholders, p. ej. [$email, $password]
     * @return mysqli_stmt  El statement ya ejecutado y con resultado bufferizado
     * @throws Exception    En caso de fallo en la preparación o ejecución
     */
    public static function ejecutar(string $sql, string $tipos = '', array $params = []): \mysqli_stmt {
        $db       = Conexion::getConexion();
        $consulta = $db->prepare($sql);
        if (!$consulta) {
            throw new Exception("Error en la preparación: " . $db->error);
        }

        // Si hay parámetros, los ligamos
        if ($tipos && count($params) > 0) {
            $consulta->bind_param($tipos, ...$params);
        }

        if (! $consulta->execute()) {
            throw new Exception("Error en la ejecución: " . $consulta->error);
        }

        // <-- Aquí bufferizamos TODO el resultset en memoria
        $consulta->store_result();

        return $consulta;
    }
}
