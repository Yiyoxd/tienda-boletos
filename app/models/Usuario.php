<?php
require_once __DIR__ ."/../../core/Conexion.php";
require_once __DIR__ ."/../../core/Consultas.php";

class Usuario {
    private $nombre, $correo, $contrasena;

    public function __construct($nombre, $correo, $contrasena) {
        $this->nombre     = $nombre;
        $this->correo     = $correo;
        $this->contrasena = $contrasena;
    }

    public function login() {
        $stmt = Consultas::ejecutar(
            "SELECT id, nombre, email, fecha_registro FROM usuarios WHERE email = ? AND password = ?",
            "ss",
            [$this->correo, $this->contrasena]
        );

        if ($stmt->num_rows === 0) {
            $stmt->free_result();
            $stmt->close();
            return false;
        }

        $stmt->bind_result($id, $nombre, $email, $fecha_registro);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();

        return [
            'id'     => $id,
            'nombre' => $nombre,
            'email'  => $email,
            'fecha_registro'=> $fecha_registro
        ];
    }

    public function registrar() {
        if ($this->correoExistente()) {
            return false;
        }

        $stmt = Consultas::ejecutar(
            "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)",
            "sss",
            [$this->nombre, $this->correo, $this->contrasena]
        );

        $ok = ($stmt->affected_rows === 1);
        $stmt->close();
        return $ok;
    }

    public function correoExistente() {
        $stmt = Consultas::ejecutar(
            "SELECT 1 FROM usuarios WHERE email = ?",
            "s",
            [$this->correo]
        );

        $exists = ($stmt->num_rows > 0);
        $stmt->free_result();
        $stmt->close();
        return $exists;
    }
}
