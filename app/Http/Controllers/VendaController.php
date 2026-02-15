<?php

namespace App\Http\Controllers;

use App\Models\ItemVenda;
use App\Models\Venda;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\Configuration\Merger;

class VendaController extends Controller
{
    public function index(Request $request)
    {
        // COMEÇO DA MUDANÇA: Adicionado o with('itens') aqui!
        $query = Venda::with('itens')->latest('sold_at');

        if ($request->filled('barber')) {
            $query->where('barber', $request->input('barber'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('category')) {
            $query->whereHas('itens', function ($q) use ($request) {
                $q->where('categoria', $request->input('category'));
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

        // pego somente 10 registros por paginas e mantenho o filtro na paginação
        $sales = $query->paginate(10)->appends($request->all());

        // pegamos os barbeiros para o filtro da view
        $barbers = Venda::select('barber')
            ->whereNotnull('barber')
            ->distinct()
            ->orderBy('barber')
            ->pluck('barber');

        $categories = ItemVenda::select('categoria')
            ->whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria');

        $statusVendas = Venda::select('status')->distinct()->pluck('status');

        return view('vendas.index', compact('sales', 'barbers', 'categories', 'statusVendas', 'methods'));
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
            'itens'              => 'required|array|min:1', // Exige que tenha pelo menos 1 item no carrinho
            'itens.*.nome'       => 'required|string',
            'itens.*.preco'      => 'required|numeric',
            'itens.*.categoria'  => 'required|string',
        ]);

        $statusVenda = 'completed';
        $paymentId = null;
        // 3. Lógica do Mercado Pago
        if ($data['payment_method'] === 'mercado_pago') {

            $respostaMP = $mercadoPagoService->criarPagamento([
                'external_reference' => $data['external_reference'],
                'valor'              => $data['amount']
            ]);

            if (isset($respostaMP['id'])) {
                $paymentId = $respostaMP['id'];
                $statusVenda = 'pending'; // Fica pendente aguardando a maquininha
            } else {
                // Se o Mercado Pago retornar erro (ex: token inválido, maquininha offline)
                return back()->with('error', 'Falha ao conectar com a maquininha do Mercado Pago. Tente novamente.');
            }

            // Como a maquininha vai processar, a venda fica pendente no banco
            $statusVenda = 'pending';
        }

        // 4. Salvar no Banco (Venda principal + Itens do Carrinho)
        try {
            DB::transaction(function () use ($data, $statusVenda, $paymentId) {

                // Cria a venda principal
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

                // Cria os itens individualmente (Corte, Pomada, etc)
                foreach ($data['itens'] as $item) {
                    $venda->itens()->create([ // Presume que você tem public function itens() na model Venda
                        'nome'           => $item['nome'],
                        'preco_unitario' => $item['preco'],
                        'quantidade'     => 1,
                        'categoria'       => $item['categoria'],
                        'barber'         => $data['barber'], // Para rastrear comissão por item, se precisar
                    ]);
                }
            });
        } catch (\Throwable $th) {
            dd("ERRO FATAL AO SALVAR NO BANCO: " . $th->getMessage());
        }

        // 5. Retornar para a lista com mensagem de sucesso
        return redirect()->route('vendas.index')
            ->with('success', 'Venda registrada com sucesso!');
    }

    public function update(Request $request, Venda $venda)
    {
        // Validação
        $dados = $request->validate([
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'sold_at'     => 'required|date',
            'barber'      => 'nullable|string'
        ]);

        // Atualiza no banco
        $venda->update($dados);

        return back()->with('success', 'Venda atualizada com sucesso!');
    }

    public function create()
    {
        // Se você for buscar os barbeiros para o select da nova venda, pode fazer aqui:
        $produtos = \App\Models\Produto::all();

        // Retorna a nova tela que vamos criar para ser o "Ponto de Venda"
        return view('vendas.create', compact('produtos'));
    }

    public function estornar($id, MercadoPagoService $mpService)
    {
        $venda = Venda::findOrFail($id);

        // Se for Mercado Pago, tentamos cancelar na API deles primeiro
        if ($venda->payment_method === 'mercado_pago' && $venda->payment_id) {
            $resposta = $mpService->estornarOrdem($venda->payment_id);

            // Se a API der erro, avisamos o usuário (exceto se já estiver cancelado lá)
            if (isset($resposta['error']) && $resposta['status'] != 400) {
                return back()->with('error', 'Erro ao estornar no Mercado Pago: ' . ($resposta['message'] ?? 'Erro desconhecido'));
            }
        }

        // Atualiza o status no seu banco de dados
        $venda->update(['status' => 'refunded']);

        return back()->with('success', 'Venda estornada com sucesso!');
    }
}
