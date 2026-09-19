<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Plano;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('plano')->get();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        $planos = Plano::where('is_active', true)->get();
        return view('clientes.create', compact('planos'));
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
}