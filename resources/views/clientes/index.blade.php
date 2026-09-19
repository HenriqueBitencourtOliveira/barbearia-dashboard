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
                            <a href="#" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop