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
        // 1. Tenta capturar o ID da Ordem do payload
        $idOrdem = $request->input('data.id') ?? $request->input('id');

        // Se não vier ID, já barra aqui e avisa no Log
        if (!$idOrdem) {
            Log::warning('Webhook MP: Payload recebido sem ID da ordem.', $request->all());
            return response()->json(['status' => 'bad_request'], 400);
        }

        // 2. Consulta a API para pegar os dados frescos
        $ordem = $mpService->consultarOrdem($idOrdem);

        // Se a API não devolver os dados certos, barra aqui
        if (empty($ordem['external_reference']) || empty($ordem['status'])) {
            Log::error("Webhook MP: Ordem {$idOrdem} retornou dados incompletos da API.", ['ordem' => $ordem]);
            return response()->json(['status' => 'ok'], 200); // Retorna 200 pro MP não ficar tentando reenviar
        }

        // 3. Busca a venda correspondente no banco
        $venda = Venda::where('external_reference', $ordem['external_reference'])->first();

        // Se não achar a venda, avisa no log e encerra
        if (!$venda) {
            Log::error("Webhook MP: Venda não encontrada para a referência: {$ordem['external_reference']}");
            return response()->json(['status' => 'ok'], 200); 
        }

        // 4. Mapeia e atualiza os status chamando a função privada abaixo
        $this->atualizarStatusVenda($venda, $ordem['status']);

        // 5. Retorna 200 OK para o Mercado Pago
        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Função separada apenas para lidar com a mudança de status
     */
    private function atualizarStatusVenda(Venda $venda, string $statusOrdem)
    {
        switch ($statusOrdem) {
            case 'processed':
                if ($venda->status !== 'completed') {
                    $venda->update(['status' => 'completed']);
                    Log::info("Sucesso: Venda {$venda->id} paga na maquininha.");
                }
                break;

            case 'canceled':
            case 'failed':
                if ($venda->status !== 'canceled') {
                    $venda->update(['status' => 'canceled']);
                    Log::warning("Falha/Cancelamento: Venda {$venda->id} ({$statusOrdem}).");
                }
                break;

            case 'refunded':
                if ($venda->status !== 'refunded') {
                    $venda->update(['status' => 'refunded']);
                    Log::info("Estorno: Venda {$venda->id} foi devolvida.");
                }
                break;

            case 'created':
            case 'at_terminal':
            case 'action_required':
                Log::info("Aguardando: Venda {$venda->id} está {$statusOrdem} na maquininha.");
                break;
                
            default:
                Log::warning("Webhook MP: Status desconhecido ({$statusOrdem}) para a Venda {$venda->id}.");
                break;
        }
    }
}