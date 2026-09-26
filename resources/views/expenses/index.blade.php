@extends('adminlte::page')

@section('title', 'Controle de Gastos')

@section('content_header')
    <h1>Controle de Gastos</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('success') }}
        </div>
    @endif

    <!-- Indicadores -->
    <div class="row">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Gastos no Mês Atual</span>
                    <span class="info-box-number">R$ {{ number_format($totalCurrentMonth, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-redo"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Mensal / Recorrente</span>
                    <span class="info-box-number">R$ {{ number_format($totalRecurring, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Não Recorrente</span>
                    <span class="info-box-number">R$ {{ number_format($totalOneTime, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Gastos -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Histórico de Despesas</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-create" style="background-color: #8b5cf6; border-color: #8b5cf6;">
                    <i class="fas fa-plus"></i> Novo Gasto
                </button>
            </div>
        </div>
        
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Data / Venc.</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Tipo</th>
                        <th>Observação</th>
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $expense)
                    <tr>
                        <td>{{ $expense->date->format('d/m/Y') }}</td>
                        <td><strong>{{ $expense->description }}</strong></td>
                        <td>R$ {{ number_format($expense->amount, 2, ',', '.') }}</td>
                        <td>
                            @if($expense->type == 'recurring')
                                <span class="badge bg-warning">Mensal / Recorrente</span>
                            @else
                                <span class="badge bg-success">Não Mensal</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($expense->observation, 30) }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#modal-edit-{{ $expense->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Editar Gasto -->
                    <div class="modal fade" id="modal-edit-{{ $expense->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Gasto</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group mb-3">
                                            <label>Descrição</label>
                                            <input type="text" name="description" class="form-control" value="{{ $expense->description }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Valor (R$)</label>
                                            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $expense->amount }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Tipo de Gasto</label>
                                            <select name="type" class="form-control" required>
                                                <option value="recurring" {{ $expense->type == 'recurring' ? 'selected' : '' }}>Mensal / Recorrente</option>
                                                <option value="one_time" {{ $expense->type == 'one_time' ? 'selected' : '' }}>Não Mensal / Pontual</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Data ou Vencimento</label>
                                            <input type="date" name="date" class="form-control" value="{{ $expense->date->format('Y-m-d') }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Observação (Opcional)</label>
                                            <textarea name="observation" class="form-control" rows="2">{{ $expense->observation }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success">Salvar Alterações</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Novo Gasto -->
    <div class="modal fade" id="modal-create" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('expenses.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Cadastrar Novo Gasto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Descrição</label>
                            <input type="text" name="description" class="form-control" placeholder="Ex: Aluguel, Internet (Velpro), Energia (Equatorial)..." required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Valor (R$)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Tipo de Gasto</label>
                            <select name="type" class="form-control" required>
                                <option value="one_time">Não Mensal / Pontual</option>
                                <option value="recurring">Mensal / Recorrente</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Data ou Vencimento</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Observação (Opcional)</label>
                            <textarea name="observation" class="form-control" rows="2" placeholder="Detalhes adicionais..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Salvar Gasto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop