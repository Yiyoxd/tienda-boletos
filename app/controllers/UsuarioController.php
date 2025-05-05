<?php

require_once "app/models/Usuario.php";
require_once ".lib/functions.php";

class UsuarioController {
    public function login(Usuario $usuario) {
        try {
            $resultado = $usuario->login();
            if ($resultado) {
                Functions::respuesta(200, true, "Login exitoso", $resultado);
            } else {
                Functions::respuesta(401, false, "Correo o contraseña incorrectos");
            }
        } catch (Exception $e) {
            Functions::error_interno();
        }
    }

    public function registrar(Usuario $usuario) {
        try {
            $correo_existente = $usuario->correoExistente();
            if ($correo_existente) {
                Functions::respuesta(
                    409, 
                    false, 
                    "Correo ya registrado"
                );
            }

            $ok = $usuario->registrar();
            $status = $ok ? 200 : 500;
            $mensaje = $ok ? "Usuario creado" : "Error al registrar";
            Functions::respuesta($status, $ok, $mensaje);

        } catch (Exception $e) {
            Functions::error_interno();
        }
    }
}