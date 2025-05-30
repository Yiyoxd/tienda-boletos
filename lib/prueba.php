<?php
require_once 'BoletoPDF.php';

$datos = [
    'cineNombre'     => "Cinetix Universidad",
    'cineID'         => 1256,
    'pelicula'       => "Spiderman",
    'fecha'          => "Domingo, 21 de diciembre de 2025",
    'hora'           => "04:20 p. m.",
    'cantidad'       => 2,
    'asientos'       => "G4, G3",
    'sala'           => "6",
    'total'          => 65.00,
    'transaccion'    => 229578,
    'metodoPago'     => "Tarjeta Bancaria",
    'imagenPelicula' => 'pelicula.jpg' // sin 'img/' si está directo en /lib
];

$generador = new GeneradorBoletoPDF();
$pdf = $generador->generar($datos);
$pdf->Output('I', 'boleto_cinetix.pdf');
