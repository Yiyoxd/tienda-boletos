<?php
require_once __DIR__ . '/../lib/PaypalService.php';

$orderID = $_GET['orderID'] ?? '';
$paypal = new PaypalService();
$resultado = $paypal->capturarOrden($orderID);

header("Content-Type: application/json");
echo json_encode($resultado);
