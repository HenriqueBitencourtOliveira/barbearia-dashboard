@extends('adminlte::page')

@section('title', 'Lista de Vendas')

@section('content_header')
    <h1>Vendas Realizadas</h1>
@stop

@section('content')

@include('vendas.components.filter')

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
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>{{ $sale->id }}</td>
                            <td>{{ $sale->description }}</td>
                            <td>{{ $sale->barber ?? '-' }}</td>
                            <td>R$ {{ number_format($sale->amount, 2, ',', '.') }}</td>

                            <td class="text-capitalize">{{ str_replace('_', ' ', $sale->payment_method) }}</td>

                            <td>{{ $sale->sold_at->format('d/m/Y H:i') }}</td>

                            <td>
                                @if ($sale->status == 'completed')
                                    <span class="badge badge-success">Concluído</span>
                                @elseif($sale->status == 'pending')
                                    <span class="badge badge-warning">Pendente</span>
                                @else
                                    <span class="badge badge-danger">{{ $sale->status }}</span>
                                @endif
                            </td>

                            <td>
                                <button type="button" class="btn btn-xs btn-secondary btn-editar" data-toggle="modal"
                                    data-target="#modal-editar-venda" data-id="{{ $sale->id }}"
                                    data-description="{{ $sale->description }}" data-barber="{{ $sale->barber }}"
                                    data-amount="{{ $sale->amount }}" data-payment_method="{{ $sale->payment_method }}"
                                    data-sold_at="{{ $sale->sold_at->format('Y-m-d\TH:i') }}">
                                    <i class="fas fa-pen"></i> </button>
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
            {{ $sales->links('pagination::bootstrap-4') }}
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
                            <input type="text" name="description" class="form-control" placeholder="Ex: Corte + Barba"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Barbeiro</label>
                            <select name="barber" class="form-control">
                                <option value="Fellipe">Fellipe</option>
                                <option value="Jhon">Jhon</option>
                                <option value="Careca">Careca</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Valor (R$)</label>
                            <input type="number" name="amount" step="0.01" class="form-control" placeholder="0.00"
                                required>
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

    {{-- Modal para editar venda --}}

    <div class="modal fade" id="modal-editar-venda" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Venda</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="form-editar-venda" action="#" method="POST">
                    @csrf
                    @method('PUT') <div class="modal-body">
                        <div class="form-group">
                            <label>Serviço / Descrição</label>
                            <input type="text" name="description" id="edit-description" class="form-control"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Barbeiro</label>
                            <select name="barber" id="edit-barber" class="form-control">
                                <option value="Fellipe">Fellipe</option>
                                <option value="Jhon">Jhon</option>
                                <option value="Careca">Careca</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Valor (R$)</label>
                            <input type="number" name="amount" id="edit-amount" step="0.01" class="form-control"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Forma de Pagamento</label>
                            <select name="payment_method" id="edit-payment_method" class="form-control">
                                <option value="money">Dinheiro</option>
                                <option value="pix_manual">Pix (Manual)</option>
                                <option value="debit_card">Débito</option>
                                <option value="credit_card">Crédito</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Data e Hora</label>
                            <input type="datetime-local" name="sold_at" id="edit-sold_at" class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')

@stop

@section('js')
    <script>
        // 1. Código anterior de erro (mantenha ele)
        @if ($errors->any())
            $('#modal-nova-venda').modal('show');
        @endif

        // 2. NOVO CÓDIGO: Atualizar horário ao abrir o modal
        $('#modal-nova-venda').on('show.bs.modal', function() {

            var now = new Date();

            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());

            var dataFormatada = now.toISOString().slice(0, 16);

            // Joga o valor dentro do input
            $('#input-data-venda').val(dataFormatada);
        });

        $('.btn-editar').on('click', function() {

            var id = $(this).data('id');
            var description = $(this).data('description');
            var barber = $(this).data('barber');
            var amount = $(this).data('amount');
            var payment_method = $(this).data('payment_method');
            var sold_at = $(this).data('sold_at');

            $('#edit-description').val(description);
            $('#edit-barber').val(barber);
            $('#edit-amount').val(amount);
            $('#edit-payment_method').val(payment_method);
            $('#edit-sold_at').val(sold_at);

            var url = "{{ route('vendas.update', ':id') }}";
            url = url.replace(':id', id);
            $('#form-editar-venda').attr('action', url);
        })
    </script>
@stop
