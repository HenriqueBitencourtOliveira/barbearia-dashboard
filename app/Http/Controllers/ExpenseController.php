<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::orderBy('date', 'desc')->get();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Indicadores do topo
        $totalRecurring = Expense::where('type', 'recurring')->sum('amount');
        $totalOneTime = Expense::where('type', 'one_time')->sum('amount');
        $totalCurrentMonth = Expense::whereMonth('date', $currentMonth)
                                   ->whereYear('date', $currentYear)
                                   ->sum('amount');

        return view('expenses.index', compact('expenses', 'totalRecurring', 'totalOneTime', 'totalCurrentMonth'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'type' => 'required|in:recurring,one_time',
            'date' => 'required|date',
            'observation' => 'nullable|string'
        ]);

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Gasto cadastrado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'type' => 'required|in:recurring,one_time',
            'date' => 'required|date',
            'observation' => 'nullable|string'
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Gasto atualizado com sucesso!');
    }
}