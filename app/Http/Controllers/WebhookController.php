<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function mercadopago(Request $request, MercadoPagoService $mpService)
    {
        // 1. Tenta capturar o ID da Ordem do payload do Webhook
        $idOrdem = $request->input('data.id') ?? $request->input('id');

        if ($idOrdem) {

            // 2. Consulta a API para pegar os dados fresquinhos da ordem
            $ordem = $mpService->consultarOrdem($idOrdem);

            if (isset($ordem['external_reference']) && isset($ordem['status'])) {

                $referenciaExterna = $ordem['external_reference'];
                $statusOrdem = $ordem['status'];

                // 3. Busca a venda correspondente no banco
                $venda = Venda::where('external_reference', $referenciaExterna)->first();

                if ($venda) {

                    // 4. Mapeia os status exatos da Maquininha (Point)
                    switch ($statusOrdem) {

                        case 'processed':
                            // Sucesso! A maquininha processou o pagamento.
                            if ($venda->status !== 'completed') {
                                $venda->update(['status' => 'completed']);
                                Log::info("Sucesso: Venda {$venda->id} paga na maquininha.");
                            }
                            break;

                        case 'canceled':
                        case 'failed':
                            // Erro de senha, sem limite ou cancelado pelo barbeiro
                            // Aqui assumimos que você quer mudar o status para 'canceled' no seu banco
                            if ($venda->status !== 'canceled') {
                                $venda->update(['status' => 'canceled']);
                                Log::warning("Falha/Cancelamento: Venda {$venda->id} ({$statusOrdem}).");
                            }
                            break;

                        case 'refunded':
                            // Estorno feito na maquininha
                            if ($venda->status !== 'refunded') {
                                // Se você não tiver o status 'refunded' na sua model, pode usar 'canceled'
                                $venda->update(['status' => 'refunded']);
                                Log::info("Estorno: Venda {$venda->id} foi devolvida.");
                            }
                            break;

                        case 'created':
                        case 'at_terminal':
                        case 'action_required':
                            // A maquininha bipou, mas o cliente ainda tá digitando a senha.
                            // Não fazemos nada com o banco, a venda continua 'pending'.
                            Log::info("Aguardando: Venda {$venda->id} está {$statusOrdem} na maquininha.");
                            break;
                    }
                }
            }
        }

        // 5. Retorna 200 OK para o Mercado Pago saber que você recebeu o recado
        return response()->json(['status' => 'ok'], 200);
    }
}
