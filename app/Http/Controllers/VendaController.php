<?php

namespace App\Http\Controllers;

use App\Models\Venda;

use Illuminate\Http\Request;
use PHPUnit\TextUI\Configuration\Merger;

class VendaController extends Controller
{
    public function index(Request $request)
    {

        // começo a query pegando o ultimo registro de venda
        $query = Venda::latest('sold_at');      

        if ($request->filled('barber')) {
            $query->where('barber', $request->input('barber'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
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


        return view('vendas.index', compact('sales', 'barbers'));
    }
    public function store(Request $request)
    {
        $request->merge(['external_reference' => $request->input('barber') .'-'. time()]); 

        $data = $request->validate([
            'description' => 'required|string|max:255',
            'barber'      => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'sold_at'     => 'required|date',
            'external_reference' => 'required|string|max:64|unique:vendas,external_reference'
        ]);

        dd($data);

        if ($request->input('payment_method') == 'mercado_pago') {
            # code...
        }

        //Adicionar campos automáticos
        $data['status'] = 'completed'; // Venda manual já está paga
        $data['payment_id'] = null;    // Não tem ID do Mercado Pago ainda

        // 3. Salvar no Banco
        Venda::create($data);

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
