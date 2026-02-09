<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MercadoPagoService
{
    protected string $token;
    protected string $pointId;

    public function __construct()
    {
        $this->token = config('services.mercado_pago.token');
        $this->pointId = config('services.mercado_pago.point_id');
    }

    public function criarPagamento(array $dadosVenda)
    {
        /** @var \Illuminate\Http\Client\Response $response */

        $response = Http::withToken($this->token)
            ->post('https://api.mercadopago.com/v1/orders', [
                'type' => 'point',
                'external_reference' => $dadosVenda['external_reference'],
                'transactions' => [
                    'payments' => [
                        [
                            'amount' => number_format($dadosVenda['valor'], 2, '.', '') // Garante formato "15.50"
                        ]
                    ]
                ],
                'config' => [
                    'point' => [
                        'terminal_id' => $this->pointId,
                    ]
                ]
            ]);

        return $response->json();
    }
}