@extends('adminlte::page')

@section('title', 'Clientes')

@section('content_header')
    <h1>Clientes</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listagem de Clientes</h3>
            <div class="card-tools">
                <a href="{{ route('clientes.create') }}" class="btn btn-sm btn-primary" style="background-color: #8b5cf6; border-color: #8b5cf6;">
                    <i class="fas fa-plus"></i> Novo Cliente
                </a>
            </div>
        </div>
        
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Plano Atual</th>
                        <th>Status</th>
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->id }}</td>
                        <td><strong>{{ $cliente->name }}</strong></td>
                        <td>{{ $cliente->email }}</td>
                        <td>
                            @if($cliente->plano)
                                <span class="badge bg-info"><i class="fas fa-tag"></i> {{ $cliente->plano->name }}</span>
                            @else
                                <span class="badge bg-secondary">Sem Plano</span>
                            @endif
                        </td>
                        <td>
                            @if($cliente->status == 'active')
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($cliente->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <!-- Botão que aciona o Modal -->
                            <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#modal-edit-{{ $cliente->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal de Edição exclusivo para este cliente -->
                    <div class="modal fade" id="modal-edit-{{ $cliente->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $cliente->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel{{ $cliente->id }}">Editar Cliente: {{ $cliente->name }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    
                                    <div class="modal-body">
                                        <!-- Nome -->
                                        <div class="form-group mb-3">
                                            <label>Nome do Cliente</label>
                                            <input type="text" name="name" class="form-control" value="{{ $cliente->name }}" required>
                                        </div>

                                        <!-- E-mail -->
                                        <div class="form-group mb-3">
                                            <label>E-mail</label>
                                            <input type="email" name="email" class="form-control" value="{{ $cliente->email }}" required>
                                        </div>

                                        <!-- Telefone -->
                                        <div class="form-group mb-3">
                                            <label>Telefone</label>
                                            <input type="text" name="phone" class="form-control" value="{{ $cliente->phone }}">
                                        </div>

                                        <!-- Plano -->
                                        <div class="form-group mb-3">
                                            <label>Plano</label>
                                            <select name="plano_id" class="form-control">
                                                <option value="">Sem plano</option>
                                                @foreach ($planos as $plano)
                                                    <option value="{{ $plano->id }}" {{ $cliente->plano_id == $plano->id ? 'selected' : '' }}>
                                                        {{ $plano->name }} - R$ {{ number_format($plano->price, 2, ',', '.') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Barbeiro -->
                                        <div class="form-group mb-3">
                                            <label>Barbeiro Responsável</label>
                                            <select name="barber" class="form-control">
                                                <option value="">Sem barbeiro</option>
                                                @foreach ($barbeiros as $barbeiro)
                                                    <option value="{{ $barbeiro }}" {{ $cliente->barber == $barbeiro ? 'selected' : '' }}>
                                                        {{ $barbeiro }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Status -->
                                        <div class="form-group mb-3">
                                            <label>Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="active" {{ $cliente->status == 'active' ? 'selected' : '' }}>Ativo</option>
                                                <option value="inactive" {{ $cliente->status == 'inactive' ? 'selected' : '' }}>Inativo</option>
                                                <option value="pending" {{ $cliente->status == 'pending' ? 'selected' : '' }}>Pendente</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
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
@stop