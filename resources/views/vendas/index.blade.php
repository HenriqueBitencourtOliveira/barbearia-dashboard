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
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
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

                            <td>
                                @if ($sale->itens && $sale->itens->count() > 0)
                                    @foreach ($sale->itens as $item)
                                        <span class="d-block text-bold">{{ $item->name }}</span>
                                    @endforeach
                                @else
                                    {{ $sale->description }}
                                @endif
                            </td>

                            {{-- Nova Coluna de Categoria --}}
                            <td>
                                @if ($sale->itens && $sale->itens->count() > 0)
                                    {{-- O unique('category') filtra as duplicadas para você --}}
                                    @foreach ($sale->itens->unique('category') as $item)
                                        <span class="d-block badge badge-info mb-1" style="max-width: fit-content;">
                                            <i class="fas fa-tag fa-xs"></i> {{ $item->category ?? 'GERAL' }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="badge badge-secondary">S/ CAT</span>
                                @endif
                            </td>

                            <td>{{ $sale->barber ?? '-' }}</td>
                            <td>R$ {{ number_format($sale->amount, 2, ',', '.') }}</td>

                            <td class="text-capitalize">{{ str_replace('_', ' ', $sale->payment_method) }}</td>

                            <td>{{ $sale->sold_at->format('d/m/Y H:i') }}</td>

                            <td>
                                @if ($sale->status == 'completed' || $sale->status == 'processed')
                                    <span class="badge badge-success">Concluído</span>
                                @elseif($sale->status == 'pending' || $sale->status == 'created' || $sale->status == 'at_terminal')
                                    <span class="badge badge-warning">Pendente</span>
                                @elseif($sale->status == 'canceled' || $sale->status == 'refunded' || $sale->status == 'failed')
                                    <span class="badge badge-danger">Estornado</span>
                                @else
                                    <span class="badge badge-secondary">{{ $sale->status }}</span>
                                @endif
                            </td>

                            <td class="d-flex">
                                {{-- Botão Editar --}}
                                <button type="button" class="btn btn-xs btn-secondary btn-editar mr-1" data-toggle="modal"
                                    data-target="#modal-editar-venda" data-id="{{ $sale->id }}"
                                    data-description="{{ $sale->description }}" data-barber="{{ $sale->barber }}"
                                    data-amount="{{ $sale->amount }}" data-payment_method="{{ $sale->payment_method }}"
                                    data-sold_at="{{ $sale->sold_at->format('Y-m-d\TH:i') }}">
                                    <i class="fas fa-pen"></i>
                                </button>

                                {{-- Botão Estornar (Só mostra se a venda não estiver cancelada) --}}
                                @if ($sale->status !== 'refunded' && $sale->payment_method == 'mercado_pago')
                                    <form action="{{ route('vendas.estornar', $sale->id) }}" method="POST"
                                        class="form-estornar d-inline-block">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-danger" title="Estornar Venda">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Nenhuma venda registrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix">
            {{ $sales->links('pagination::bootstrap-4') }}
        </div>
    </div>

    {{-- Modal para editar venda (MANTIDO) --}}
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
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Serviço / Descrição</label>
                            <input type="text" name="description" id="edit-description" class="form-control" required>
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
                                <option value="mercado_pago">Maquina</option>
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
        // Script de Edição mantido
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Intercepta o envio de qualquer formulário que tenha a classe 'form-estornar'
        $('.form-estornar').on('submit', function(e) {
            e.preventDefault(); // Pausa o envio padrão do HTML

            const form = this; // Guarda a referência do formulário clicado

            Swal.fire({
                title: 'Atenção!',
                text: "Deseja realmente estornar esta venda? O valor será devolvido ao cliente e a maquininha será notificada.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', // Vermelho para ação destrutiva
                cancelButtonColor: '#6c757d', // Cinza para cancelar
                confirmButtonText: '<i class="fas fa-undo"></i> Sim, estornar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                // Se o usuário clicar no botão "Sim, estornar!"
                if (result.isConfirmed) {
                    // Mostra um aviso de carregamento enquanto o Laravel processa o estorno
                    Swal.fire({
                        title: 'Processando...',
                        text: 'Comunicando com o Mercado Pago, aguarde.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Dispara o formulário de verdade
                    form.submit();
                }
            });
        });
    </script>
@stop
