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
                <a href="{{ route('planos.create') }}" class="btn btn-sm btn-primary" style="background-color: #8b5cf6; border-color: #8b5cf6;">
                    <i class="fas fa-plus"></i> Novo Plano
                </a>
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
                        <td>
                            @if($plano->is_active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-danger">Inativo</span>
                            @endif
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop