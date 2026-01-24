@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Barbearia Dashboard</h1>
@stop

@section('content')
    <p>Bem-vindo ao seu painel financeiro.</p>
    
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>150</h3>
                    <p>Novas Vendas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="#" class="small-box-footer">Mais info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script> console.log('AdminLTE funcionando!'); </script>
@stop