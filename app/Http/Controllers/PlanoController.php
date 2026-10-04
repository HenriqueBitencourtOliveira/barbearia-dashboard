<?php

namespace App\Http\Controllers;

use App\Models\Plano;
use Illuminate\Http\Request;

class PlanoController extends Controller
{
    public function index()
    {
        $planos = Plano::all();
        return view('planos.index', compact('planos')); 
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'cuts_included' => 'required|integer|min:0',
        ]);

        Plano::create($validated);

        return redirect()->route('planos.index')->with('success', 'Plano cadastrado com sucesso.');
    }

    public function update(Request $request, Plano $plano)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'cuts_included' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $plano->update($validated);

        return redirect()->route('planos.index')->with('success', 'Plano atualizado com sucesso.');
    }
}