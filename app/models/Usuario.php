<?php
require_once "core/Conexion.php";
require_once "core/Consultas.php";

class Usuario {
    private $nombre, $correo, $contrasena;

    public function __construct($nombre, $correo, $contrasena) {
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->contrasena = $contrasena;
    }

    public function login() {
        $consulta = Consultas::ejecutar (
            "Select * from usuarios where email = ? and password = ?",
            "ss",
            [$this->correo, $this->contrasena]
        );
        
        return $consulta->get_result()->fetch_assoc();
    }

    public function registrar() {
        if ($this->correoExistente()) {
            return false;
        }
        
        $consulta = Consultas::ejecutar (
            "insert into usuarios (nombre, email, password) values (?, ?, ?)",
            "sss",
            [$this->nombre, $this->correo, $this->contrasena]
        );

        return $consulta->num_rows == 1;
    }

    public function correoExistente() {
        $consulta = Consultas::ejecutar (
            "select * from usuarios where email = ?",
            "s",
            $this->correo
        );

        return $consulta->num_rows > 0;
    }
}