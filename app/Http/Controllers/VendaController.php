<?php

namespace App\Http\Controllers;

use App\Models\Venda;

use Illuminate\Http\Request;

class VendaController extends Controller
{
    public function index()
    {
        // Pega as vendas ordenadas pela data (mais recentes primeiro)
        // Pagina de 10 em 10
        $vendas = Venda::latest('sold_at')->paginate(10);

        return view('vendas.index', compact('vendas'));
    }
    public function store(Request $request)
    {
        //Validação (Garante que não venha dado vazio ou errado)
        $dados = $request->validate([
            'description' => 'required|string|max:255',
            'barber'      => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'sold_at'     => 'required|date',
        ]);

        //Adicionar campos automáticos
        $dados['status'] = 'completed'; // Venda manual já está paga
        $dados['payment_id'] = null;    // Não tem ID do Mercado Pago ainda

        // 3. Salvar no Banco
        Venda::create($dados);

        // 4. Retornar para a lista com mensagem de sucesso
        return back()->with('success', 'Venda registrada com sucesso!');
    }
}
