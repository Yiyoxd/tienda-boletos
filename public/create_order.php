<?php
require_once __DIR__ . '/../lib/PaypalService.php';   // ajusta el path si lo metes en /public

header('Content-Type: application/json');
$data  = json_decode(file_get_contents('php://input'), true);
$total = $data['total']        ?? 0;
$desc  = $data['descripcion']  ?? 'Reserva Cinetix';

$paypal = new PaypalService();
$res    = $paypal->crearOrden($total, $desc);

echo json_encode($res);   //  Debe regresar:  { "id": "ORDER_ID_GENERADA" }
