<?php

namespace App\Http\Controllers;

use App\Models\Venda;

use Illuminate\Http\Request;

class VendaController extends Controller
{
    public function index(Request $request)
    {
        //Pega o filtro da URL (se existir)
        $barberSelect = $request->input('barber');

        // começo a query pegando o ultimo registro de venda
        $query = Venda::latest('sold_at');      
       
        // verifico se foi selecionada algum barbeiro se sim adiciono o filtro na query
        if($barberSelect){
            $query->where('barber', $barberSelect);
        }        

        // pego somente 10 registros por paginas e mantenho o filtro na paginação
        $sales = $query->paginate(10)->appends($request->all());
        
        // pegamos os barbeiros para o filtro da view
        $barbers = Venda::select('barber')
                            ->whereNotnull('barber')
                            ->distinct()
                            ->orderBy('barber')
                            ->pluck('barber');


        return view('vendas.index', compact('sales', 'barbers', 'barberSelect'));
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
}
