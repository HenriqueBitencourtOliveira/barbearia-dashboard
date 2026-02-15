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
            ->withHeaders([
                'X-Idempotency-Key' => $dadosVenda['external_reference'] // <-- Chave de segurança adicionada aqui!
            ])
            ->post('https://api.mercadopago.com/v1/orders', [
                'type' => 'point',
                'external_reference' => $dadosVenda['external_reference'],
                'transactions' => [
                    'payments' => [
                        [
                            // O (float) garante que vá como número e não texto
                            'amount' => number_format($dadosVenda['valor'], 2, '.', '')
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

    public function consultarOrdem($id)
    {

        /** @var \Illuminate\Http\Client\Response $response */

        $response = Http::withToken($this->token)
            ->get("https://api.mercadopago.com/v1/orders/{$id}");

        return $response->json();
    }

    public function estornarOrdem($paymentId)
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withToken($this->token)
            ->withHeaders([
                // Usamos uniqid() para garantir que a chave nunca se repita
                'X-Idempotency-Key' => uniqid()
            ])
            ->post("https://api.mercadopago.com/v1/orders/{$paymentId}/refund", (object) []);

        return $response->json();
    }
}
