<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Importante para apagar imagens

class ProdutoController extends Controller
{
    public function create()
    {
        // Busca os produtos do mais novo para o mais antigo para a lista
        $produtos = Produto::latest()->get();

        return view('produtos.create', compact('produtos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'categoria' => 'required|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validação da imagem
        ]);

        // Lógica de upload da imagem
        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            // Salva na pasta 'public/produtos' e gera um nome único
            $imagePath = $request->file('imagem')->store('produtos', 'public');
            $data['imagem'] = $imagePath;
        }

        Produto::create($data);

        return back()->with('success', 'Item cadastrado com sucesso!');
    }

    // Adicione este método para a exclusão
    public function destroy(Produto $produto)
    {
        // Se tiver imagem, apaga ela do servidor para não ocupar espaço
        if ($produto->imagem) {
            Storage::disk('public')->delete($produto->imagem);
        }

        $produto->delete();

        return back()->with('success', 'Produto removido com sucesso!');
    }

    public function update(Request $request, Produto $produto)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'categoria' => 'required|string',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Se o usuário mandou uma foto nova
        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            // Apaga a antiga do servidor (se existir)
            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }
            // Salva a nova
            $data['imagem'] = $request->file('imagem')->store('produtos', 'public');
        }

        $produto->update($data);

        return back()->with('success', 'Produto atualizado com sucesso!');
    }
}
