<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    public function create()
    {
        // Busca os produtos do mais novo para o mais antigo para a lista
        $produtos = Produto::latest()->get();

        return view('produtos.create', compact('produtos'));
    }

    /**
     * Salva um novo produto no banco de dados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Mudou para image
        ]);

        // Lógica de upload da imagem (tudo para image agora)
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Salva na pasta 'public/produtos' e gera um nome único
            $imagePath = $request->file('image')->store('produtos', 'public');
            $data['image'] = $imagePath;
        }

        Produto::create($data);

        return back()->with('success', 'Item cadastrado com sucesso!');
    }


    /**
     * Remove um produto do banco de dados e apaga a imagem do servidor se houver.
     *
     * @param Produto $produto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Produto $produto)
    {
        // Acessa a coluna em inglês: image
        if ($produto->image) {
            Storage::disk('public')->delete($produto->image);
        }

        $produto->delete();

        return back()->with('success', 'Produto removido com sucesso!');
    }

    /**
     * Atualiza um produto no banco de dados e apaga a imagem do servidor se houver uma nova.
     *
     * @param Request $request
     * @param Produto $produto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Produto $produto)
    {
        // Passado tudo para inglês para bater com o banco
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Se o usuário mandou uma foto nova
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Apaga a antiga do servidor (se existir)
            if ($produto->image) {
                Storage::disk('public')->delete($produto->image);
            }
            // Salva a nova
            $data['image'] = $request->file('image')->store('produtos', 'public');
        }

        $produto->update($data);

        return back()->with('success', 'Produto atualizado com sucesso!');
    }
}
