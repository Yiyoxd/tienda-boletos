<?php

require_once __DIR__ ."/../app/models/Usuario.php";
require_once __DIR__ ."/../lib/functions.php";
require_once __DIR__ ."/../app/controllers/UsuarioController.php";
require_once __DIR__ ."/../lib/LimpiarDatos.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$accion = $_GET["accion"] ?? "";

try {
    $nombre = LimpiarDatos::normalizarTexto($data["nombre"] ?? "");
    $correo = LimpiarDatos::normalizarTexto($data["correo"] ??"");
    $contrasena = $data["password"] ?? "";

    $usuario = new Usuario($nombre, $correo, $contrasena);
    $controller = new UsuarioController();

    switch ($accion) {
        case "login":
            $controller->login($usuario);
            break;
        case "registrar":
            $controller->registrar($usuario);
            break;
        default:
            Functions::accionInvalida();
    }
    
} catch (Exception $e) {
    Functions::respuesta(400, false, $e->getMessage());
}