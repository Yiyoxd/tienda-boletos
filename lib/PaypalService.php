<?php

class PaypalService {
    private $config;
    private $token;

    public function __construct() {
        $this->config = require __DIR__ . '/../config/paypal.php';
        $this->token = $this->obtenerToken();
    }

    private function obtenerToken() {
        $auth = base64_encode("{$this->config['client_id']}:{$this->config['secret']}");

        $res = file_get_contents($this->config['base_url'] . "/v1/oauth2/token", false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Authorization: Basic $auth\r\nContent-Type: application/x-www-form-urlencoded",
                'content' => "grant_type=client_credentials"
            ]
        ]));

        return json_decode($res, true)['access_token'] ?? null;
    }

    public function crearOrden($total, $descripcion = 'Reserva Cinetix') {
        $data = json_encode([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => 'MXN',
                    'value' => number_format($total, 2, '.', '')
                ],
                'description' => $descripcion
            ]]
        ]);

        return $this->request('/v2/checkout/orders', 'POST', $data);
    }

    public function capturarOrden($orderID) {
        $respuesta = $this->request("/v2/checkout/orders/$orderID/capture", 'POST');

        $metodoPago = 'PayPal';
        $marca = '';
        $ultimos = '';
        if (isset($respuesta['purchase_units'][0]['payments']['captures'][0]['payment_source']['card'])) {
            $card = $respuesta['purchase_units'][0]['payments']['captures'][0]['payment_source']['card'];
            $marca = $card['brand'] ?? '';
            $ultimos = $card['last_digits'] ?? '';
            $metodoPago = "Tarjeta $marca ****$ultimos";
        }

        $nombre = ($respuesta['payer']['name']['given_name'] ?? '') . ' ' . ($respuesta['payer']['name']['surname'] ?? '');
        $correo = $respuesta['payer']['email_address'] ?? '';

        return [
            'status' => $respuesta['status'] ?? 'UNKNOWN',
            'id_transaccion' => $respuesta['id'] ?? '',
            'metodo_pago' => $metodoPago,
            'nombre' => $nombre,
            'correo' => $correo
        ];
    }



    private function request($endpoint, $method, $body = null) {
        $context = [
            'http' => [
                'method'  => $method,
                'header'  => "Authorization: Bearer {$this->token}\r\nContent-Type: application/json",
            ]
        ];

        if ($body) {
            $context['http']['content'] = $body;
        }

        $response = file_get_contents($this->config['base_url'] . $endpoint, false, stream_context_create($context));
        return json_decode($response, true);
    }
}
