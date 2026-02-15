@extends('adminlte::page')

@section('title', 'Nova Venda (PDV)')

@section('content_header')
    <h1>Frente de Caixa (PDV)</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card bg-dark text-white shadow">
            <div class="card-header border-bottom-0" style="background-color: #343a40;">
                <h3 class="card-title"><i class="fas fa-cut fa-fw"></i> Serviços e Produtos</h3>
            </div>
            <div class="card-body" style="max-height: 75vh; overflow-y: auto;">
                
                @if(isset($produtos) && $produtos->count() > 0)
                    @foreach($produtos->groupBy('categoria') as $categoria => $itens)
                        <h5 class="mb-3 mt-2 text-uppercase" style="color: #8b5cf6; border-bottom: 1px solid #444; padding-bottom: 5px;">
                            <i class="fas fa-tags fa-xs"></i> {{ $categoria }}
                        </h5>
                        
                        <div class="row mb-4">
                            @foreach($itens as $item)
                                <div class="col-6 col-sm-4 col-md-3 mb-3">
                                    <button class="btn btn-outline-light w-100 p-2 text-center h-100 d-flex flex-column align-items-center justify-content-between" 
                                            style="border-radius: 12px; transition: transform 0.1s;"
                                            onmousedown="this.style.transform='scale(0.95)';" 
                                            onmouseup="this.style.transform='scale(1)';"
                                            onmouseleave="this.style.transform='scale(1)';"
                                            onclick="adicionarItem('{{ $item->nome }}', {{ $item->preco }}, '{{ $categoria }}')">
                                        
                                        @if($item->imagem)
                                            <img src="{{ asset('storage/' . $item->imagem) }}" alt="{{ $item->nome }}" class="rounded mb-2 shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                        @else
                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 80px; height: 80px;">
                                                <i class="fas fa-box text-muted fa-2x"></i>
                                            </div>
                                        @endif

                                        <div class="w-100">
                                            <span class="d-block font-weight-bold text-truncate" style="font-size: 0.9rem;" title="{{ $item->nome }}">
                                                {{ $item->nome }}
                                            </span>
                                            <span class="d-block text-success font-weight-bold" style="font-size: 1.1rem;">
                                                R$ {{ number_format($item->preco, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                        Nenhum produto cadastrado ainda. Vá em "Cadastrar produto" no menu lateral.
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div class="col-md-4">
        <form action="{{ route('vendas.store') }}" method="POST" id="formVenda">
            @csrf
            <div class="card bg-dark text-white shadow">
                <div class="card-header" style="background-color: #343a40;">
                    <h3 class="card-title"><i class="fas fa-shopping-cart fa-fw"></i> Resumo da Venda</h3>
                </div>
                
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="barber">Barbeiro</label>
                        <select name="barber" id="barber" class="form-control bg-secondary text-white border-0" required>
                            <option value="">Selecione o profissional...</option>
                            <option value="Fellipe">Fellipe</option>
                            <option value="Jhon">Jhon</option>
                            <option value="Careca">Careca</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="sold_at">Data/Hora</label>
                        <input type="datetime-local" name="sold_at" class="form-control bg-secondary text-white border-0" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>

                    <div class="form-group mb-4">
                        <label for="payment_method">Forma de Pagamento</label>
                        <select name="payment_method" id="payment_method" class="form-control bg-secondary text-white border-0" required>
                            <option value="money">Dinheiro</option>
                            <option value="mercado_pago">Marcado pago</option>
                        </select>
                    </div>

                    <hr class="border-secondary">

                    <label>Itens Selecionados:</label>
                    <ul id="listaCarrinho" class="list-group list-group-flush mb-3 rounded" style="max-height: 250px; overflow-y: auto;">
                        <li class="list-group-item bg-secondary text-center text-muted" id="carrinhoVazio">
                            Nenhum item adicionado.
                        </li>
                    </ul>

                    <div id="inputsOcultos"></div>

                    <input type="hidden" name="amount" id="totalVendaInput" value="0">
                    
                    <input type="hidden" name="description" id="descricaoVendaInput" value="Venda Balcão">

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <h4 class="mb-0 text-success text-bold">Total: R$ <span id="totalExibicao">0,00</span></h4>
                    </div>
                </div>
                
                <div class="card-footer border-top border-secondary">
                    <button type="submit" class="btn btn-success btn-lg w-100" style="background-color: #8b5cf6; border-color: #8b5cf6;" id="btnFinalizar" disabled>
                        <i class="fas fa-check-circle fa-fw"></i> Finalizar Venda
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    let carrinho = [];

    function adicionarItem(nome, preco, categoria) {
        carrinho.push({ nome, preco, categoria });
        atualizarInterface();
    }

    function removerItem(index) {
        carrinho.splice(index, 1);
        atualizarInterface();
    }

    function atualizarInterface() {
        const lista = document.getElementById('listaCarrinho');
        const inputs = document.getElementById('inputsOcultos');
        const spanTotal = document.getElementById('totalExibicao');
        const inputTotal = document.getElementById('totalVendaInput');
        const inputDescricao = document.getElementById('descricaoVendaInput');
        const btnFinalizar = document.getElementById('btnFinalizar');

        lista.innerHTML = '';
        inputs.innerHTML = '';
        
        let total = 0;
        let nomesItens = [];

        if (carrinho.length === 0) {
            lista.innerHTML = '<li class="list-group-item bg-secondary text-center text-muted" id="carrinhoVazio">Nenhum item adicionado.</li>';
            btnFinalizar.disabled = true;
            inputDescricao.value = "Venda Balcão";
        } else {
            btnFinalizar.disabled = false;
            
            carrinho.forEach((item, index) => {
                total += item.preco;
                nomesItens.push(item.nome);

                lista.innerHTML += `
                    <li class="list-group-item bg-secondary text-white d-flex justify-content-between align-items-center border-bottom border-dark px-2 py-1">
                        <div>
                            <span class="d-block font-weight-bold" style="font-size: 0.9rem;">${item.nome}</span>
                            <small class="text-light">R$ ${item.preco.toFixed(2).replace('.', ',')}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removerItem(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </li>
                `;

                inputs.innerHTML += `
                    <input type="hidden" name="itens[${index}][nome]" value="${item.nome}">
                    <input type="hidden" name="itens[${index}][preco]" value="${item.preco}">
                    <input type="hidden" name="itens[${index}][categoria]" value="${item.categoria}">
                `;
            });

            inputDescricao.value = nomesItens.join(', ');
        }

        spanTotal.innerText = total.toFixed(2).replace('.', ',');
        inputTotal.value = total;
    }
</script>
@stop