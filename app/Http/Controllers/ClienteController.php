<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Plano;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientesQuery = Cliente::with('plano');

        if ($request->filled('name')) {
            $clientesQuery->where('name', 'like', '%' . trim($request->input('name')) . '%');
        }

        if ($request->filled('barber')) {
            $clientesQuery->where('barber', $request->input('barber'));
        }

        $clientes = $clientesQuery->get();
        $planos = Plano::where('is_active', true)->get();
        
        // Busca os barbeiros para o select do modal de edição
        $barbeirosVendas = Venda::whereNotNull('barber')->pluck('barber')->toArray();
        $barbeirosClientes = Cliente::whereNotNull('barber')->pluck('barber')->toArray();
        $barbeiros = collect(array_merge($barbeirosVendas, $barbeirosClientes))->unique()->sort()->values();
        $barbeirosFiltro = Cliente::whereNotNull('barber')
            ->where('barber', '!=', '')
            ->distinct()
            ->orderBy('barber')
            ->pluck('barber');

        return view('clientes.index', compact('clientes', 'planos', 'barbeiros', 'barbeirosFiltro'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'phone' => 'nullable|string|max:20',
            'plano_id' => 'nullable|exists:planos,id',
            'barber' => 'nullable|string|max:255',
        ]);

        Cliente::create($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso.');
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

    public function registrarCorte(Cliente $cliente)
    {
        $resultado = DB::transaction(function () use ($cliente) {
            $cliente = Cliente::with('plano')
                ->lockForUpdate()
                ->findOrFail($cliente->id);

            if (!$cliente->plano) {
                return ['error' => 'Este cliente não possui um plano vinculado.'];
            }

            if ($cliente->status !== 'active') {
                return ['error' => 'Não é possível registrar cortes para um cliente inativo ou pendente.'];
            }

            $limite = (int) $cliente->plano->cuts_included;
            $usados = (int) $cliente->cuts_used;

            if ($usados >= $limite) {
                return ['error' => 'Este cliente já utilizou todos os cortes do plano.'];
            }

            $cliente->cuts_used = $usados + 1;
            $cliente->save();
            $cliente->cortes()->create(['user_id' => auth()->id()]);

            return ['restantes' => $limite - $cliente->cuts_used];
        });

        if (isset($resultado['error'])) {
            return back()
                ->with('error', $resultado['error'])
                ->with('open_cut_modal', $cliente->id);
        }

        $cortesRestantes = $resultado['restantes'];
        $mensagemRestantes = $cortesRestantes === 1 ? 'corte restante.' : 'cortes restantes.';

        return back()->with('success', "Baixa registrada. {$cortesRestantes} {$mensagemRestantes}");
    }
}