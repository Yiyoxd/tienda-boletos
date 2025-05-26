<?php

require_once __DIR__ . "/../models/Usuario.php";
require_once __DIR__ . "/../../lib/functions.php";

class UsuarioController {
    public function login(Usuario $usuario) {
        try {
            $resultado = $usuario->login();

            if ($resultado) {
                // ⭐ Aquí se guarda la sesión para mantener el login
                session_regenerate_id(true); // Seguridad: nuevo ID de sesión
                $_SESSION['usuario_id'] = $resultado['id'];
                $_SESSION['nombre']     = $resultado['nombre'];

                Functions::respuesta(200, true, "Login exitoso", $resultado);
            } else {
                Functions::respuesta(401, false, "Correo o contraseña incorrectos");
            }
        } catch (Exception $e) {
            Functions::respuesta(
                500,
                false,
                "Error Interno",
                ["exception" => $e->getMessage()]
            );
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
                return;
            }

            $ok = $usuario->registrar();
            $status  = $ok ? 200 : 500;
            $mensaje = $ok ? "Usuario creado" : "Error al registrar";
            Functions::respuesta($status, $ok, $mensaje);

        } catch (Exception $e) {
            Functions::respuesta(
                500,
                false,
                "Error Interno",
                ["exception" => $e->getMessage()]
            );
        }
    }
}
