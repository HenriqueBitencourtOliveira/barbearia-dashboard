<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MercadoPagoService
{
    private function credentialsForBarber(string $barber): ?array
    {
        $credentials = config("services.mercado_pago.barbers.{$barber}");

        if (empty($credentials['token']) || empty($credentials['point_id'])) {
            return null;
        }

        return $credentials;
    }

    public function criarPagamento(array $dadosVenda, string $barber)
    {
        $credentials = $this->credentialsForBarber($barber);

        if (!$credentials) {
            return ['error' => 'credentials_not_configured'];
        }

        /** @var \Illuminate\Http\Client\Response $response */

        $response = Http::withToken($credentials['token'])
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
                        'terminal_id' => $credentials['point_id'],
                    ]
                ]
            ]);

        return $response->json();
    }

    public function consultarOrdem($id, string $barber)
    {
        $credentials = $this->credentialsForBarber($barber);

        if (!$credentials) {
            return ['error' => 'credentials_not_configured'];
        }

        /** @var \Illuminate\Http\Client\Response $response */

        $response = Http::withToken($credentials['token'])
            ->get("https://api.mercadopago.com/v1/orders/{$id}");

        return $response->json();
    }

    public function estornarOrdem($paymentId, string $barber)
    {
        $credentials = $this->credentialsForBarber($barber);

        if (!$credentials) {
            return ['error' => 'credentials_not_configured'];
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withToken($credentials['token'])
            ->withHeaders([
                // Usamos uniqid() para garantir que a chave nunca se repita
                'X-Idempotency-Key' => uniqid()
            ])
            ->post("https://api.mercadopago.com/v1/orders/{$paymentId}/refund", (object) []);

        return $response->json();
    }
}
