@extends('adminlte::page')

@section('title', 'Planos')

@section('content_header')
    <h1>Planos de Assinatura</h1>
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
            <h3 class="card-title">Modelos de Planos</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-create-plano" style="background-color: #8b5cf6; border-color: #8b5cf6;">
                    <i class="fas fa-plus"></i> Novo Plano
                </button>
            </div>
        </div>
        
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Duração (Dias)</th>
                        <th>Cortes incluídos</th>
                        <th>Status</th>
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($planos as $plano)
                    <tr>
                        <td>{{ $plano->id }}</td>
                        <td><strong>{{ $plano->name }}</strong></td>
                        <td>R$ {{ number_format($plano->price, 2, ',', '.') }}</td>
                        <td>{{ $plano->duration_in_days }}</td>
                        <td>{{ $plano->cuts_included }}</td>
                        <td>
                            @if($plano->is_active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-danger">Inativo</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#modal-edit-plano-{{ $plano->id }}" aria-label="Editar {{ $plano->name }}" title="Editar plano">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="modal-edit-plano-{{ $plano->id }}" tabindex="-1" role="dialog" aria-labelledby="modalEditPlanoLabel{{ $plano->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('planos.update', $plano->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="form_context" value="edit_plano">
                                    <input type="hidden" name="plan_id" value="{{ $plano->id }}">

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalEditPlanoLabel{{ $plano->id }}">Editar Plano: {{ $plano->name }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="edit-plan-name-{{ $plano->id }}">Nome do Plano</label>
                                            <input type="text" name="name" id="edit-plan-name-{{ $plano->id }}" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $plano->name) }}" required>
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="edit-plan-price-{{ $plano->id }}">Preço (R$)</label>
                                            <input type="number" step="0.01" min="0" name="price" id="edit-plan-price-{{ $plano->id }}" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $plano->price) }}" required>
                                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="edit-plan-duration-{{ $plano->id }}">Duração (dias)</label>
                                            <input type="number" min="1" name="duration_in_days" id="edit-plan-duration-{{ $plano->id }}" class="form-control @error('duration_in_days') is-invalid @enderror" value="{{ old('duration_in_days', $plano->duration_in_days) }}" required>
                                            @error('duration_in_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="edit-plan-cuts-{{ $plano->id }}">Cortes incluídos</label>
                                            <input type="number" min="0" name="cuts_included" id="edit-plan-cuts-{{ $plano->id }}" class="form-control @error('cuts_included') is-invalid @enderror" value="{{ old('cuts_included', $plano->cuts_included) }}" required>
                                            @error('cuts_included')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="form-group mb-0">
                                            <label for="edit-plan-status-{{ $plano->id }}">Status</label>
                                            <select name="is_active" id="edit-plan-status-{{ $plano->id }}" class="form-control @error('is_active') is-invalid @enderror" required>
                                                <option value="1" {{ old('is_active', $plano->is_active) == '1' ? 'selected' : '' }}>Ativo</option>
                                                <option value="0" {{ old('is_active', $plano->is_active) == '0' ? 'selected' : '' }}>Inativo</option>
                                            </select>
                                            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

    <div class="modal fade" id="modal-create-plano" tabindex="-1" role="dialog" aria-labelledby="modalCreatePlanoLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('planos.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_context" value="create_plano">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCreatePlanoLabel">Novo Plano</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new-plan-name">Nome do Plano</label>
                            <input type="text" name="name" id="new-plan-name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="new-plan-price">Preço (R$)</label>
                            <input type="number" step="0.01" min="0" name="price" id="new-plan-price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="new-plan-duration">Duração (dias)</label>
                            <input type="number" min="1" name="duration_in_days" id="new-plan-duration" class="form-control @error('duration_in_days') is-invalid @enderror" value="{{ old('duration_in_days', 30) }}" required>
                            @error('duration_in_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="new-plan-cuts">Cortes incluídos</label>
                            <input type="number" min="0" name="cuts_included" id="new-plan-cuts" class="form-control @error('cuts_included') is-invalid @enderror" value="{{ old('cuts_included', 4) }}" required>
                            @error('cuts_included')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Salvar Plano</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    @if (old('form_context') === 'create_plano')
        <script>
            $(function () {
                $('#modal-create-plano').modal('show');
            });
        </script>
    @elseif (old('form_context') === 'edit_plano' && old('plan_id'))
        <script>
            $(function () {
                $('#modal-edit-plano-{{ old('plan_id') }}').modal('show');
            });
        </script>
    @endif
@stop