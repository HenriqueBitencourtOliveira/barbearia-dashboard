@extends('adminlte::page')

@section('title', 'Cadastrar Cliente')

@section('content_header')
    <h1>Cadastrar Novo Plano</h1>
@stop
@section('content')
<div class="container">

    <form action="{{ route('planos.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="name">Nome do Plano</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="price">Preço (R$)</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="duration_in_days">Duração (Dias)</label>
            <input type="number" name="duration_in_days" id="duration_in_days" class="form-control" value="30" required>
        </div>

        <button type="submit" class="btn btn-success">Salvar Plano</button>
        <a href="{{ route('planos.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@endsection