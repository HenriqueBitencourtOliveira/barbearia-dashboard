@extends('adminlte::page')

@section('title', 'Cadastrar Cliente')

@section('content_header')
    <h1>Cadastrar Novo Cliente</h1>
@stop

@section('content')
<div class="container">

    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="name">Nome do Cliente</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="phone">Telefone</label>
            <input type="text" name="phone" id="phone" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label for="plano_id">Vincular a um Plano (Opcional)</label>
            <select name="plano_id" id="plano_id" class="form-control">
                <option value="">Selecione um plano...</option>
                @foreach ($planos as $plano)
                    <option value="{{ $plano->id }}">{{ $plano->name }} - R$ {{ number_format($plano->price, 2, ',', '.') }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Salvar Cliente</button>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@endsection