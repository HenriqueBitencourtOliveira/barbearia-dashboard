@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">

            <form method="GET" action="{{ route('dashboard.index') }}">
                <div class="row">


                    <div class="col-md-1">
                        <div class="form-group">
                            <label>Barbeiro</label>
                            <select name="barber" class="form-control" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach ($barbeiros as $barber)
                                    <option value="{{ $barber }}" {{ $barberSelect == $barber ? 'selected' : '' }}>
                                        {{ $barber }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-group">
                            <a href="{{ route('dashboard.index') }}" class="btn btn-default ml-2">Limpar</a>
                        </div>
                    </div>

                </div>
            </form>

            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Fluxo de Vendas: Comparativo Anual</h3>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="barChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>

            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    @include('dashboard.components.weekly_sales_chart')
                </div>

                <div class="col-md-6">
                    @include('dashboard.components.daily_sales_chart')
                </div>

            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- CSS Adicional se precisar --}}
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(function() {
            // --- DADOS DO GRÁFICO (Aqui colocaremos os dados do Banco depois) ---
            var areaChartData = {
                labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto',
                    'Setembro', 'Outubro', 'Novembro', 'Dezembro'
                ],
                datasets: [{
                        label: 'Ano Atual ({{ date('Y') }})',
                        backgroundColor: '#ED3237', // Azul
                        borderColor: '#b08d55',
                        pointRadius: false,
                        pointColor: '#3b8bba',
                        pointStrokeColor: 'rgba(60,141,188,1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data: {!! json_encode($currentSales) !!} // <--- DADOS FAKE ANO ATUAL
                    },
                    {
                        label: 'Ano Anterior ({{ date('Y') - 1 }})',
                        backgroundColor: '#343a40', // Cinza
                        borderColor: '#343a40',
                        pointRadius: false,
                        pointColor: 'rgba(210, 214, 222, 1)',
                        pointStrokeColor: '#c1c7d1',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data: {!! json_encode($previousSales) !!} // <--- DADOS FAKE ANO PASSADO
                    },
                ]
            }

            // --- CONFIGURAÇÃO VISUAL ---
            var barChartCanvas = $('#barChart').get(0).getContext('2d')
            var barChartData = $.extend(true, {}, areaChartData)

            // Inverter as ordens se quiser (às vezes o dataset 0 fica atrás)
            var temp0 = areaChartData.datasets[0]
            var temp1 = areaChartData.datasets[1]
            barChartData.datasets[0] = temp1
            barChartData.datasets[1] = temp0

            var barChartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                datasetFill: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }

            new Chart(barChartCanvas, {
                type: 'bar',
                data: barChartData,
                options: barChartOptions
            })
        })
    </script>
@stop
