<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Plano;
use App\Models\Venda;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('plano')->get();
        $planos = Plano::where('is_active', true)->get();
        
        // Busca os barbeiros para o select do modal de edição
        $barbeirosVendas = Venda::whereNotNull('barber')->pluck('barber')->toArray();
        $barbeirosClientes = Cliente::whereNotNull('barber')->pluck('barber')->toArray();
        $barbeiros = collect(array_merge($barbeirosVendas, $barbeirosClientes))->unique()->sort()->values();

        return view('clientes.index', compact('clientes', 'planos', 'barbeiros'));
    }

    public function create()
    {
        $planos = Plano::where('is_active', true)->get();
        
        // Busca os barbeiros já registrados nas vendas e clientes para popular o select
        $barbeirosVendas = Venda::whereNotNull('barber')->pluck('barber')->toArray();
        $barbeirosClientes = Cliente::whereNotNull('barber')->pluck('barber')->toArray();
        
        $barbeiros = collect(array_merge($barbeirosVendas, $barbeirosClientes))
            ->unique()
            ->sort()
            ->values();

        // Se preferir uma lista fixa em vez de buscar do banco, comente o código acima e use:
        // $barbeiros = ['Careca', 'Fellipe', 'João'];

        return view('clientes.create', compact('planos', 'barbeiros'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'phone' => 'nullable|string|max:20',
            'plano_id' => 'nullable|exists:planos,id',
        ]);

        Cliente::create($validated);

        return redirect()->route('clientes.index')->with('success', 'Customer registered successfully.');
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id, // Ignora o email atual na validação unique
            'phone' => 'nullable|string|max:20',
            'plano_id' => 'nullable|exists:planos,id',
            'barber' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,pending',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente atualizado com sucesso.');
    }
}