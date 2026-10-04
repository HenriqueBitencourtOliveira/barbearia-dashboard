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
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listagem de Clientes</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-create-cliente" style="background-color: #8b5cf6; border-color: #8b5cf6;">
                    <i class="fas fa-plus"></i> Novo Cliente
                </button>
            </div>
        </div>

        <div class="card-body border-bottom">
            <form action="{{ route('clientes.index') }}" method="GET" class="row align-items-end">
                <div class="col-md-5 mb-2">
                    <label for="filter-name">Nome do cliente</label>
                    <input type="search" name="name" id="filter-name" class="form-control" value="{{ request('name') }}" placeholder="Buscar pelo nome">
                </div>
                <div class="col-md-4 mb-2">
                    <label for="filter-barber">Barbeiro</label>
                    <select name="barber" id="filter-barber" class="form-control">
                        <option value="">Todos os barbeiros</option>
                        @foreach ($barbeirosFiltro as $barbeiro)
                            <option value="{{ $barbeiro }}" {{ request('barber') === $barbeiro ? 'selected' : '' }}>{{ $barbeiro }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Limpar</a>
                </div>
            </form>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Plano Atual</th>
                        <th>Cortes</th>
                        <th>Barbeiro</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $cliente)
                    @php($cortesRestantes = $cliente->plano ? max((int) $cliente->plano->cuts_included - (int) $cliente->cuts_used, 0) : 0)
                    @php($podeRegistrarCorte = $cliente->plano && $cliente->status === 'active' && $cortesRestantes > 0)
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
                            @if($cliente->plano)
                                <strong>{{ $cliente->cuts_used }} / {{ $cliente->plano->cuts_included }}</strong>
                                <span class="badge {{ $cortesRestantes > 0 ? 'bg-success' : 'bg-secondary' }}">{{ $cortesRestantes }} restantes</span>
                            @else
                                <span class="text-muted">Sem plano</span>
                            @endif
                        </td>
                        <td>{{ $cliente->barber ?: 'Não informado' }}</td>
                        <td>
                            @if($cliente->status == 'active')
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($cliente->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal-corte-{{ $cliente->id }}" aria-label="Registrar corte para {{ $cliente->name }}" title="Registrar corte">
                                <i class="fas fa-cut"></i>
                            </button>
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

        @foreach ($clientes as $cliente)
            @php($cortesRestantes = $cliente->plano ? max((int) $cliente->plano->cuts_included - (int) $cliente->cuts_used, 0) : 0)
            @php($podeRegistrarCorte = $cliente->plano && $cliente->status === 'active' && $cortesRestantes > 0)
            <div class="modal fade" id="modal-corte-{{ $cliente->id }}" tabindex="-1" role="dialog" aria-labelledby="modalCorteLabel{{ $cliente->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalCorteLabel{{ $cliente->id }}">Registrar corte: {{ $cliente->name }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if($cliente->plano)
                                <p class="mb-2">Plano: <strong>{{ $cliente->plano->name }}</strong></p>
                                <p class="mb-2">Cortes utilizados: <strong>{{ $cliente->cuts_used }} de {{ $cliente->plano->cuts_included }}</strong></p>
                                <p class="mb-0">Cortes restantes: <strong>{{ $cortesRestantes }}</strong></p>
                            @else
                                <div class="alert alert-warning mb-0">Este cliente não possui um plano vinculado.</div>
                            @endif

                            @if($cliente->plano && $cliente->status !== 'active')
                                <div class="alert alert-warning mt-3 mb-0">O cliente precisa estar ativo para registrar um corte.</div>
                            @elseif($cliente->plano && $cortesRestantes === 0)
                                <div class="alert alert-warning mt-3 mb-0">Todos os cortes deste plano já foram utilizados.</div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <form action="{{ route('clientes.cortes.store', $cliente) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success" {{ $podeRegistrarCorte ? '' : 'disabled' }}>
                                    <i class="fas fa-cut"></i> Dar baixa em 1 corte
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="modal fade" id="modal-create-cliente" tabindex="-1" role="dialog" aria-labelledby="modalCreateClienteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_context" value="create_cliente">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCreateClienteLabel">Novo Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new-client-name">Nome do Cliente</label>
                            <input type="text" name="name" id="new-client-name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="new-client-email">E-mail</label>
                            <input type="email" name="email" id="new-client-email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="new-client-phone">Telefone</label>
                            <input type="text" name="phone" id="new-client-phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="new-client-plan">Plano</label>
                            <select name="plano_id" id="new-client-plan" class="form-control @error('plano_id') is-invalid @enderror">
                                <option value="">Sem plano</option>
                                @foreach ($planos as $plano)
                                    <option value="{{ $plano->id }}" {{ old('plano_id') == $plano->id ? 'selected' : '' }}>
                                        {{ $plano->name }} - R$ {{ number_format($plano->price, 2, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('plano_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="new-client-barber">Barbeiro Responsável</label>
                            <select name="barber" id="new-client-barber" class="form-control @error('barber') is-invalid @enderror">
                                <option value="">Sem barbeiro</option>
                                @foreach ($barbeiros as $barbeiro)
                                    <option value="{{ $barbeiro }}" {{ old('barber') == $barbeiro ? 'selected' : '' }}>{{ $barbeiro }}</option>
                                @endforeach
                            </select>
                            @error('barber')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Salvar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    @if (old('form_context') === 'create_cliente')
        <script>
            $(function () {
                $('#modal-create-cliente').modal('show');
            });
        </script>
    @elseif (session('open_cut_modal'))
        <script>
            $(function () {
                $('#modal-corte-{{ session('open_cut_modal') }}').modal('show');
            });
        </script>
    @endif
@stop