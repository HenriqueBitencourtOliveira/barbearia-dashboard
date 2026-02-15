<div class="card card-primary card-outline collapsed-card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-1"></i> Filtros Avançados
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>

    <div class="card-body" style="display: none;">
        <form method="GET" action="{{ route('vendas.index') }}">
            <div class="row">

                {{-- 1. Barbeiro --}}
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Barbeiro</label>
                        <select name="barber" class="form-control">
                            <option value="">Todos</option>
                            @foreach ($barbers as $barber)
                                <option value="{{ $barber }}"
                                    {{ request('barber') == $barber ? 'selected' : '' }}>
                                    {{ $barber }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- 2. Categoria (NOVO) --}}
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Categoria</label>
                        <select name="category" class="form-control">
                            <option value="">Todas</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- 3. Tipo de Pagamento --}}
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Pagamento</label>
                        <select name="payment_method" class="form-control">
                            <option value="">Todos</option>
                            <option value="money" {{ request('payment_method') == 'money' ? 'selected' : '' }}>Dinheiro
                            </option>
                            <option value="mercado_pago"
                                {{ request('payment_method') == 'mercado_pago' ? 'selected' : '' }}>Maquina</option>
                        </select>
                    </div>
                </div>

                {{-- 4. Data --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Data da Venda</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                </div>

                {{-- 5. Descrição --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Descrição</label>
                        <input type="text" name="description" class="form-control" placeholder="Ex: Corte"
                            value="{{ request('description') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Todos</option>
                            @foreach ($statusVendas as $status)
                                @php
                                    // Mapeia o valor do banco para o nome em português
                                    $statusNomes = [
                                        'completed' => 'Concluído',
                                        'processed' => 'Concluído',
                                        'pending' => 'Pendente',
                                        'created' => 'Pendente',
                                        'canceled' => 'Cancelado',
                                        'refunded' => 'Estornado',
                                        'failed' => 'Falha',
                                    ];
                                    $nomeExibicao = $statusNomes[$status] ?? ucfirst($status);
                                @endphp

                                {{-- O "value" continua sendo o original em inglês para o Controller entender --}}
                                <option value="{{ $status }}"
                                    {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $nomeExibicao }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <a href="{{ route('vendas.index') }}" class="btn btn-default mr-1">
                        <i class="fas fa-times"></i> Limpar Filtros
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
