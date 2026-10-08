<?php

namespace App\Http\Controllers;

use App\Models\ItemVenda;
use App\Models\Produto;
use App\Models\Venda;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venda::with('itens')->latest('sold_at');

        if ($request->filled('barber')) {
            $query->where('barber', $request->input('barber'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('category')) {
            $query->whereHas('itens', function ($q) use ($request) {
                $q->where('category', $request->input('category'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date')) {
            $query->whereDate('sold_at', $request->input('date'));
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->input('description') . '%');
        }

        $sales = $query->paginate(10)->appends($request->all());

        $barbers = Venda::select('barber')
            ->whereNotnull('barber')
            ->distinct()
            ->orderBy('barber')
            ->pluck('barber');

        $categories = ItemVenda::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        $statusVendas = Venda::select('status')->distinct()->pluck('status');

        return view('vendas.index', compact('sales', 'barbers', 'categories', 'statusVendas'));
    }

        public function store(Request $request, MercadoPagoService $mercadoPagoService)
        {
            // 1. Gerar referência externa única
            $request->merge(['external_reference' => $request->input('barber') . '-' . time()]);

            $data = $request->validate([
                'description'        => 'required|string|max:255',
                'barber'             => 'required|string|max:255',
                'amount'             => 'required|numeric|min:0',
                'payment_method'     => 'required|string',
                'sold_at'            => 'required|date',
                'external_reference' => 'required|string|max:64|unique:vendas,external_reference',
                'items'              => 'required|array|min:1', 
                'items.*.name'       => 'required|string',
                'items.*.price'      => 'required|numeric',     
                'items.*.category'   => 'required|string',      
                'items.*.quantity'   => 'required|integer|min:1',
            ]);

            $statusVenda = 'completed';
            $paymentId = null;

            // 3. Lógica do Mercado Pago
            if ($data['payment_method'] === 'mercado_pago') {

                $respostaMP = $mercadoPagoService->criarPagamento([
                    'external_reference' => $data['external_reference'],
                    'valor'              => $data['amount']
                ], $data['barber']);

                if (($respostaMP['error'] ?? null) === 'credentials_not_configured') {
                    return back()->withInput()->with('error_modal', 'Credenciais do Mercado Pago não configuradas para este barbeiro.');
                }

                if (isset($respostaMP['id'])) {
                    $paymentId = $respostaMP['id'];
                    $statusVenda = 'pending'; 
                } else {
                    return back()->withInput()->with('error_modal', 'Falha ao conectar com a maquininha do Mercado Pago. Tente novamente.');
                }
                $statusVenda = 'pending';
            }

            // 4. Salvar no Banco
            try {
                DB::transaction(function () use ($data, $statusVenda, $paymentId) {

                    $venda = Venda::create([
                        'description'        => $data['description'],
                        'amount'             => $data['amount'],
                        'payment_method'     => $data['payment_method'],
                        'status'             => $statusVenda,
                        'payment_id'         => $paymentId,
                        'sold_at'            => $data['sold_at'],
                        'barber'             => $data['barber'],
                        'external_reference' => $data['external_reference'],
                    ]);

                    foreach ($data['items'] as $item) { 
                        $venda->itens()->create([ 
                            'name'       => $item['name'],
                            'unit_price' => $item['price'],
                            'quantity'   => $item['quantity'],
                            'category'   => $item['category'],
                            'barber'     => $data['barber'],
                        ]);
                    }
                });
            } catch (\Throwable $th) {
                Log::error("Erro Crítico ao salvar venda: " . $th->getMessage());

                return back()
                    ->withInput()
                    ->with('error_modal', 'Ops! Tivemos um problema interno ao registrar os itens no banco de dados. Por favor, tente novamente.');
            }

            return redirect()->route('vendas.index')
                ->with('success', 'Venda registrada com sucesso!');
        }

    public function update(Request $request, Venda $venda)
    {
        $dados = $request->validate([
            'description'    => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'sold_at'        => 'required|date',
            'barber'         => 'nullable|string'
        ]);

        $venda->update($dados);

        return back()->with('success', 'Venda atualizada com sucesso!');
    }

    public function create()
    {
        $produtos = Produto::all();
        return view('vendas.create', compact('produtos'));
    }

    public function estornar($id, MercadoPagoService $mpService)
    {
        $venda = Venda::findOrFail($id);

        if ($venda->payment_method === 'mercado_pago' && $venda->payment_id) {
            $resposta = $mpService->estornarOrdem($venda->payment_id, $venda->barber);

            if (($resposta['error'] ?? null) === 'credentials_not_configured') {
                return back()->with('error', 'Credenciais do Mercado Pago não configuradas para este barbeiro.');
            }

            if (isset($resposta['error']) && $resposta['status'] != 400) {
                return back()->with('error', 'Erro ao estornar no Mercado Pago: ' . ($resposta['message'] ?? 'Erro desconhecido'));
            }
        }

        $venda->update(['status' => 'refunded']);

        return back()->with('success', 'Venda estornada com sucesso!');
    }

    public function sincronizarStatus($id, MercadoPagoService $mpService)
    {
        $venda = Venda::findOrFail($id);

        // Verifica se a venda é via Mercado Pago e se possui um ID de pagamento
        if ($venda->payment_method !== 'mercado_pago' || !$venda->payment_id) {
            return back()->with('error', 'Apenas vendas processadas pelo Mercado Pago podem ser sincronizadas.');
        }

        // Consulta a ordem diretamente no Mercado Pago
        $ordem = $mpService->consultarOrdem($venda->payment_id, $venda->barber);

        if (empty($ordem['status'])) {
            Log::error("Sincronização Manual: Ordem {$venda->payment_id} retornou sem status.", ['ordem' => $ordem]);
            return back()->with('error', 'Não foi possível obter o status da venda no Mercado Pago.');
        }

        $statusOrdem = $ordem['status'];
        $statusAtual = $venda->status;
        $novoStatus = $statusAtual;

        // Mapeia o status do MP para o sistema
        switch ($statusOrdem) {
            case 'processed':
                $novoStatus = 'completed';
                break;
            case 'canceled':
            case 'failed':
                $novoStatus = 'canceled';
                break;
            case 'refunded':
                $novoStatus = 'refunded';
                break;
            case 'created':
            case 'at_terminal':
            case 'action_required':
                // Continua pendente, não muda nada
                break;
            default:
                Log::warning("Sincronização Manual: Status desconhecido ({$statusOrdem}) para a Venda {$venda->id}.");
                break;
        }

        // Salva apenas se o status realmente mudou
        if ($novoStatus !== $statusAtual) {
            $venda->update(['status' => $novoStatus]);
            return back()->with('success', "Status atualizado com sucesso de '{$statusAtual}' para '{$novoStatus}'.");
        }

        return back()->with('info', 'O status já está atualizado e não precisou de alterações.');
    }
}