@extends('adminlte::page')

@section('title', 'Gerenciar Produtos')

@section('content_header')
    <h1>Gerenciar Produtos e Serviços</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card bg-dark text-white shadow">
            <div class="card-header border-bottom-0">
                <h3 class="card-title"><i class="fas fa-box fa-fw"></i> Cadastrar Novo Item</h3>
            </div>
            
            <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row align-items-center">
                        <div class="col-md-4 text-center border-right border-secondary px-4">
                            <label>Foto do Produto</label>
                            <div class="mt-2 mb-3">
                                <img id="image-preview" src="https://placehold.co/150x150/1f2937/a855f7?text=Sem+Foto" alt="Preview" class="img-circle border border-secondary" style="height: 120px; width: 120px; object-fit: cover;">
                            </div>
                            <div class="custom-file text-left">
                                <input type="file" name="imagem" class="custom-file-input" id="imagemInput" accept="image/*">
                                <label class="custom-file-label bg-secondary text-white border-0" for="imagemInput" data-browse="Buscar">Escolher...</label>
                            </div>
                        </div>

                        <div class="col-md-8 px-4">
                            <div class="form-group mb-3">
                                <label for="nome">Nome do Serviço ou Produto</label>
                                <input type="text" name="nome" class="form-control bg-secondary text-white border-0" placeholder="Ex: Pomada Matte..." required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="preco">Preço</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-secondary text-white border-0">R$</span>
                                            </div>
                                            <input type="number" step="0.01" name="preco" class="form-control bg-secondary text-white border-0" placeholder="0.00" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="categoria">Categoria</label>
                                        <select name="categoria" class="form-control bg-secondary text-white border-0" required>
                                            <option value="">Selecione...</option>
                                            <option value="CORTES">Cortes e Barba</option>
                                            <option value="PRODUTOS">Produtos</option>
                                            <option value="BEBIDAS">Bebidas</option>
                                            <option value="OUTROS">Outros</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-dark text-right border-top border-secondary">
                    <button type="submit" class="btn btn-success px-4" style="background-color: #8b5cf6; border-color: #8b5cf6;">
                        <i class="fas fa-save fa-fw"></i> Salvar Cadastro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-10 mx-auto">
        <div class="card bg-dark text-white shadow">
            <div class="card-header border-bottom-0" style="background-color: #343a40;">
                <h3 class="card-title"><i class="fas fa-list fa-fw"></i> Itens Cadastrados</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-dark text-nowrap mb-0">
                    <thead class="bg-secondary">
                        <tr>
                            <th class="text-center" width="10%">Imagem</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th width="15%" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produtos as $produto)
                            <tr>
                                <td class="text-center">
                                    @if($produto->imagem)
                                        <img src="{{ asset('storage/' . $produto->imagem) }}" class="img-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-box text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle font-weight-bold">{{ $produto->nome }}</td>
                                <td class="align-middle"><span class="badge" style="background-color: #8b5cf6;">{{ $produto->categoria }}</span></td>
                                <td class="align-middle text-success text-bold">R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                                <td class="align-middle text-center">
                                    
                                    <button class="btn btn-sm btn-primary btn-editar" 
                                        data-toggle="modal" 
                                        data-target="#modal-editar-produto"
                                        data-id="{{ $produto->id }}"
                                        data-nome="{{ $produto->nome }}"
                                        data-preco="{{ $produto->preco }}"
                                        data-categoria="{{ $produto->categoria }}"
                                        data-imagem="{{ $produto->imagem ? asset('storage/' . $produto->imagem) : '' }}">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    
                                    <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Nenhum item cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-editar-produto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Editar Produto</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="form-editar-produto" action="#" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    
                    <div class="text-center mb-4">
                        <img id="edit-image-preview" src="https://placehold.co/150x150/1f2937/a855f7?text=Sem+Foto" class="img-circle border border-secondary mb-2" style="height: 100px; width: 100px; object-fit: cover;">
                        <div class="custom-file text-left">
                            <input type="file" name="imagem" class="custom-file-input" id="editImagemInput" accept="image/*">
                            <label class="custom-file-label bg-secondary text-white border-0" for="editImagemInput" data-browse="Alterar">Escolher nova foto...</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nome do Serviço ou Produto</label>
                        <input type="text" name="nome" id="edit-nome" class="form-control bg-secondary text-white border-0" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Preço (R$)</label>
                            <input type="number" step="0.01" name="preco" id="edit-preco" class="form-control bg-secondary text-white border-0" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Categoria</label>
                            <select name="categoria" id="edit-categoria" class="form-control bg-secondary text-white border-0" required>
                                <option value="CORTES">Cortes e Barba</option>
                                <option value="PRODUTOS">Produtos</option>
                                <option value="BEBIDAS">Bebidas</option>
                                <option value="OUTROS">Outros</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" style="background-color: #8b5cf6; border-color: #8b5cf6;">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    // Preview da imagem no Cadastro
    document.getElementById('imagemInput').addEventListener('change', function(e) {
        if (e.target.files[0]) {
            document.querySelector('.custom-file-label').textContent = e.target.files[0].name;
            const reader = new FileReader();
            reader.onload = function(e) { document.getElementById('image-preview').src = e.target.result; }
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Preview da imagem na Edição
    document.getElementById('editImagemInput').addEventListener('change', function(e) {
        if (e.target.files[0]) {
            document.querySelector('#editImagemInput + label').textContent = e.target.files[0].name;
            const reader = new FileReader();
            reader.onload = function(e) { document.getElementById('edit-image-preview').src = e.target.result; }
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Lógica para abrir o Modal de Edição com os dados
    $('.btn-editar').on('click', function() {
        var id = $(this).data('id');
        var nome = $(this).data('nome');
        var preco = $(this).data('preco');
        var categoria = $(this).data('categoria');
        var imagem = $(this).data('imagem');

        // Preenche os inputs
        $('#edit-nome').val(nome);
        $('#edit-preco').val(preco);
        $('#edit-categoria').val(categoria);

        // Arruma a imagem do preview no modal
        if (imagem) {
            $('#edit-image-preview').attr('src', imagem);
        } else {
            $('#edit-image-preview').attr('src', 'https://placehold.co/150x150/1f2937/a855f7?text=Sem+Foto');
        }

        // Atualiza a URL do formulário para o ID correto
        var url = "{{ route('produtos.update', ':id') }}";
        url = url.replace(':id', id);
        $('#form-editar-produto').attr('action', url);
    });
</script>
@stop