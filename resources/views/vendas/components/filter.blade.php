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
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Barbeiro</label>
                        <select name="barber" class="form-control">
                            <option value="">Todos</option>
                            @foreach ($barbers as $barber)
                                <option value="{{ $barber }}" {{ request('barber') == $barber ? 'selected' : '' }}>
                                    {{ $barber }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- 2. Tipo de Pagamento --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Pagamento</label>
                        <select name="payment_method" class="form-control">
                            <option value="">Todos</option>
                            <option value="money" {{ request('payment_method') == 'money' ? 'selected' : '' }}>Dinheiro</option>
                            <option value="pix_manual" {{ request('payment_method') == 'pix_manual' ? 'selected' : '' }}>Pix</option>
                            <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>Crédito</option>
                            <option value="debit_card" {{ request('payment_method') == 'debit_card' ? 'selected' : '' }}>Débito</option>
                        </select>
                    </div>
                </div>

                {{-- 3. Data --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Data da Venda</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                </div>

                {{-- 4. Descrição --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Descrição</label>
                        <input type="text" name="description" class="form-control" 
                               placeholder="Ex: Corte" value="{{ request('description') }}">
                    </div>
                </div>

            </div> <div class="row">
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