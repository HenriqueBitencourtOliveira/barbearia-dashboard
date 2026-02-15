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

                    @if (isset($produtos) && $produtos->count() > 0)
                        @foreach ($produtos->groupBy('category') as $category => $items)
                            <h5 class="mb-3 mt-2 text-uppercase"
                                style="color: #8b5cf6; border-bottom: 1px solid #444; padding-bottom: 5px;">
                                <i class="fas fa-tags fa-xs"></i> {{ $category }}
                            </h5>

                            <div class="row mb-4">
                                @foreach ($items as $item)
                                    <div class="col-6 col-sm-4 col-md-3 mb-3">
                                        <button
                                            class="btn btn-outline-light w-100 p-2 text-center h-100 d-flex flex-column align-items-center justify-content-between"
                                            style="border-radius: 12px; transition: transform 0.1s;"
                                            onmousedown="this.style.transform='scale(0.95)';"
                                            onmouseup="this.style.transform='scale(1)';"
                                            onmouseleave="this.style.transform='scale(1)';" {{-- MUDANÇA JS: Usando addItem, name, price e category --}}
                                            onclick="addItem('{{ $item->name }}', {{ $item->price }}, '{{ $item->category }}')">

                                            {{-- MUDANÇA: 'image' --}}
                                            @if ($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                                    class="rounded mb-2 shadow-sm"
                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center mb-2 shadow-sm"
                                                    style="width: 80px; height: 80px;">
                                                    <i class="fas fa-box text-muted fa-2x"></i>
                                                </div>
                                            @endif

                                            <div class="w-100">
                                                {{-- MUDANÇA: 'name' --}}
                                                <span class="d-block font-weight-bold text-truncate"
                                                    style="font-size: 0.9rem;" title="{{ $item->name }}">
                                                    {{ $item->name }}
                                                </span>
                                                {{-- MUDANÇA: 'price' --}}
                                                <span class="d-block text-success font-weight-bold"
                                                    style="font-size: 1.1rem;">
                                                    R$ {{ number_format($item->price, 2, ',', '.') }}
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
                            {{-- MUDANÇA: Adicionado old('barber') para manter selecionado se der erro --}}
                            <select name="barber" id="barber" class="form-control bg-secondary text-white border-0"
                                required>
                                <option value="">Selecione o profissional...</option>
                                <option value="Fellipe" {{ old('barber') == 'Fellipe' ? 'selected' : '' }}>Fellipe</option>
                                <option value="Jhon" {{ old('barber') == 'Jhon' ? 'selected' : '' }}>Jhon</option>
                                <option value="Careca" {{ old('barber') == 'Careca' ? 'selected' : '' }}>Careca</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="sold_at">Data/Hora</label>
                            {{-- MUDANÇA: old('sold_at') --}}
                            <input type="datetime-local" name="sold_at"
                                class="form-control bg-secondary text-white border-0"
                                value="{{ old('sold_at', now()->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        <div class="form-group mb-4">
                            <label for="payment_method">Forma de Pagamento</label>
                            {{-- MUDANÇA: old('payment_method') --}}
                            <select name="payment_method" id="payment_method"
                                class="form-control bg-secondary text-white border-0" required>
                                <option value="money" {{ old('payment_method') == 'money' ? 'selected' : '' }}>Dinheiro
                                </option>
                                <option value="mercado_pago"
                                    {{ old('payment_method') == 'mercado_pago' ? 'selected' : '' }}>Mercado pago</option>
                            </select>
                        </div>

                        <hr class="border-secondary">

                        <label>Itens Selecionados:</label>
                        <ul id="listaCarrinho" class="list-group list-group-flush mb-3 rounded"
                            style="max-height: 250px; overflow-y: auto;">
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
                        <button type="submit" class="btn btn-success btn-lg w-100"
                            style="background-color: #8b5cf6; border-color: #8b5cf6;" id="btnFinalizar" disabled>
                            <i class="fas fa-check-circle fa-fw"></i> Finalizar Venda
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. Lógica do Carrinho em Inglês
        // Aqui usamos o old() para recuperar o carrinho se o formulário retornar com erro
        let cart = {!! json_encode(old('items', [])) !!};

        // Se houver dados velhos (old), converte do formato de objeto do Laravel para o array do JS
        if (Object.keys(cart).length > 0) {
            let recoveredCart = [];
            for (let key in cart) {
                recoveredCart.push({
                    name: cart[key].name,
                    price: parseFloat(cart[key].price),
                    category: cart[key].category
                });
            }
            cart = recoveredCart;

            // Renderiza a tela após recuperar
            window.onload = function() {
                updateInterface();
            };
        }

        function addItem(name, price, category) {
            cart.push({
                name,
                price,
                category
            });
            updateInterface();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            updateInterface();
        }

        function updateInterface() {
            const lista = document.getElementById('listaCarrinho');
            const inputs = document.getElementById('inputsOcultos');
            const spanTotal = document.getElementById('totalExibicao');
            const inputTotal = document.getElementById('totalVendaInput');
            const inputDescricao = document.getElementById('descricaoVendaInput');
            const btnFinalizar = document.getElementById('btnFinalizar');

            lista.innerHTML = '';
            inputs.innerHTML = '';

            let total = 0;
            let itemNames = [];

            if (cart.length === 0) {
                lista.innerHTML =
                    '<li class="list-group-item bg-secondary text-center text-muted" id="carrinhoVazio">Nenhum item adicionado.</li>';
                btnFinalizar.disabled = true;
                inputDescricao.value = "Venda Balcão";
            } else {
                btnFinalizar.disabled = false;

                cart.forEach((item, index) => {
                    total += item.price;
                    itemNames.push(item.name);

                    lista.innerHTML += `
                    <li class="list-group-item bg-secondary text-white d-flex justify-content-between align-items-center border-bottom border-dark px-2 py-1">
                        <div>
                            <span class="d-block font-weight-bold" style="font-size: 0.9rem;">${item.name}</span>
                            <small class="text-light">R$ ${item.price.toFixed(2).replace('.', ',')}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </li>
                `;

                    // Montando os inputs escondidos em inglês (items, name, price, category)
                    inputs.innerHTML += `
                    <input type="hidden" name="items[${index}][name]" value="${item.name}">
                    <input type="hidden" name="items[${index}][price]" value="${item.price}">
                    <input type="hidden" name="items[${index}][category]" value="${item.category}">
                `;
                });

                inputDescricao.value = itemNames.join(', ');
            }

            spanTotal.innerText = total.toFixed(2).replace('.', ',');
            inputTotal.value = total;
        }

        // 2. Disparo dos Modais (SweetAlert) com base nos erros/sucessos
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('error_modal'))
                Swal.fire({
                    icon: 'error',
                    title: 'Problema na Venda',
                    text: '{{ session('error_modal') }}',
                    confirmButtonColor: '#d33'
                });
            @endif

            @if (session('success_modal'))
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: '{{ session('success_modal') }}',
                    confirmButtonColor: '#8b5cf6' // Usando a mesma cor roxa do seu botão!
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção aos dados!',
                    html: 'Tivemos um problema com os dados informados:<br> @foreach ($errors->all() as $error) <span class="d-block mt-1">{{ $error }}</span> @endforeach',
                    confirmButtonColor: '#ffc107'
                });
            @endif
        });
    </script>
@stop
