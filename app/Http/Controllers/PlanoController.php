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

    public function create()
    {
        return view('planos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'duration_in_days' => 'required|integer',
        ]);

        Plano::create($validated);

        return redirect()->route('planos.index')->with('success', 'Plan created successfully.');
    }
}