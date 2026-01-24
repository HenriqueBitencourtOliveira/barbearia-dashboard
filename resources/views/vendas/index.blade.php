@extends('adminlte::page')

@section('title', 'Lista de Vendas')

@section('content_header')
    <h1>Vendas Realizadas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Histórico</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-nova-venda">
                    <i class="fas fa-plus"></i> Nova Venda
                </button>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descrição</th>
                        <th>Barbeiro</th>
                        <th>Valor</th>
                        <th>Pagamento</th>
                        <th>Data</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vendas as $venda)
                        <tr>
                            <td>{{ $venda->id }}</td>
                            <td>{{ $venda->description }}</td>
                            <td>{{ $venda->barber ?? '-' }}</td>
                            <td>R$ {{ number_format($venda->amount, 2, ',', '.') }}</td>

                            <td class="text-capitalize">{{ str_replace('_', ' ', $venda->payment_method) }}</td>

                            <td>{{ $venda->sold_at->format('d/m/Y H:i') }}</td>

                            <td>
                                @if ($venda->status == 'completed')
                                    <span class="badge badge-success">Concluído</span>
                                @elseif($venda->status == 'pending')
                                    <span class="badge badge-warning">Pendente</span>
                                @else
                                    <span class="badge badge-danger">{{ $venda->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Nenhuma venda registrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix">
            {{ $vendas->links('pagination::bootstrap-4') }}
        </div>
    </div>


    {{-- Modal para nova venda --}}
    <div class="modal fade" id="modal-nova-venda" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Venda</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('vendas.store') }}" method="POST">
                @csrf <div class="modal-body">
                    <div class="form-group">
                        <label>Serviço / Descrição</label>
                        <input type="text" name="description" class="form-control" placeholder="Ex: Corte + Barba" required>
                    </div>

                    <div class="form-group">
                        <label>Barbeiro</label>
                        <select name="barber" class="form-control">
                            <option value="Felipe">Felipe</option>
                            <option value="Jhon">Jhon</option>
                            <option value="Careca">Careca</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Valor (R$)</label>
                        <input type="number" name="amount" step="0.01" class="form-control" placeholder="0.00" required>
                    </div>

                    <div class="form-group">
                        <label>Forma de Pagamento</label>
                        <select name="payment_method" class="form-control">
                            <option value="money">Dinheiro</option>
                            <option value="pix_manual">Pix (Manual)</option>
                            <option value="debit_card">Débito</option>
                            <option value="credit_card">Crédito</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Data e Hora</label>
                        <input type="datetime-local" name="sold_at" class="form-control" id="input-data-venda" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Venda</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
    {{-- Se precisar de CSS extra --}}
@stop

@section('js')
<script>
    // 1. Código anterior de erro (mantenha ele)
    @if ($errors->any())
        $('#modal-nova-venda').modal('show');
    @endif

    // 2. NOVO CÓDIGO: Atualizar horário ao abrir o modal
    $('#modal-nova-venda').on('show.bs.modal', function () {
        
        var now = new Date();
        
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        
        var dataFormatada = now.toISOString().slice(0,16);
        
        // Joga o valor dentro do input
        $('#input-data-venda').val(dataFormatada);
    });
</script>
@stop
