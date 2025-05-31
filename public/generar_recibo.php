<?php
require_once __DIR__ . "/../lib/BoletoPDF.php";

header('Content-Type: application/pdf');

// Validar parámetro
if (!isset($_GET['json'])) {
    http_response_code(400);
    echo 'Falta el parámetro JSON.';
    exit;
}

// Decodificar el JSON del parámetro GET
$datos = json_decode(urldecode($_GET['json']), true);

// Validar datos básicos
$requeridos = ['cineNombre', 'cineID', 'pelicula', 'fecha', 'hora', 'cantidad', 'asientos', 'sala', 'total', 'transaccion', 'metodoPago', 'imagenPelicula'];
$faltantes = array_filter($requeridos, fn($campo) => !isset($datos[$campo]));

if (count($faltantes) > 0) {
    http_response_code(422);
    echo 'Faltan datos obligatorios: ' . implode(', ', $faltantes);
    exit;
}

// Generar PDF
$generador = new GeneradorBoletoPDF();
$pdf = $generador->generar($datos);
$pdf->Output('I', 'boleto_cinetix.pdf');
exit;
    