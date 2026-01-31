@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
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
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            


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
    
@stop


@section('js')
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(function() {
        // 1. Pegamos o contexto do Canvas para criar o Degradê (Gradient)
        var canvas = $('#barChart').get(0);
        var ctx = canvas.getContext('2d');

        // --- CRIANDO OS DEGRADÊS (O segredo do visual) ---
        
        // Degradê Roxo (Ano Atual)
        var gradientCurrent = ctx.createLinearGradient(0, 0, 0, 400);
        gradientCurrent.addColorStop(0, 'rgba(124, 58, 237, 0.5)'); // Roxo forte no topo
        gradientCurrent.addColorStop(1, 'rgba(124, 58, 237, 0.0)'); // Transparente em baixo

        // Degradê Azul (Ano Anterior)
        var gradientPrev = ctx.createLinearGradient(0, 0, 0, 400);
        gradientPrev.addColorStop(0, 'rgba(14, 165, 233, 0.3)'); // Azul médio
        gradientPrev.addColorStop(1, 'rgba(14, 165, 233, 0.0)'); // Transparente

        // 2. Configuração dos Dados
        var areaChartData = {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [
                {
                    label: 'Ano Atual ({{ date("Y") }})',
                    data: {!! json_encode($currentSales) !!}, // Seus dados do Laravel
                    
                    // Estilo da Linha Roxa
                    borderColor: '#7c3aed', // Cor da linha sólida
                    backgroundColor: gradientCurrent, // O degradê que criamos acima
                    borderWidth: 3,
                    
                    // Configurações para ficar igual à imagem
                    fill: true,          // Preenche embaixo da linha
                    tension: 0.4,        // Cria a curva suave (Wavy)
                    pointRadius: 0,      // Esconde as bolinhas (pontos)
                    pointHoverRadius: 6, // Mostra a bolinha só quando passa o mouse
                    pointBackgroundColor: '#fff' // Bolinha branca ao passar o mouse
                },
                {
                    label: 'Ano Anterior ({{ date("Y") - 1 }})',
                    data: {!! json_encode($previousSales) !!}, // Seus dados do Laravel
                    
                    // Estilo da Linha Azul
                    borderColor: '#0ea5e9',
                    backgroundColor: gradientPrev,
                    borderWidth: 2, // Um pouco mais fina para dar destaque ao ano atual
                    
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff'
                }
            ]
        };

        // 3. Configurações do Gráfico
        var chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',      // Mostra tooltip dos dois anos ao mesmo tempo
                intersect: false,
            },
            plugins: {
                legend: {
                    labels: { 
                        color: '#fff',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    } // Legenda branca
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)', // Grade bem suave
                        drawBorder: false
                    },
                    ticks: { 
                        color: '#adb5bd',
                        font: {
                            size: 14
                        }
                    }
                },
                x: {
                    grid: { display: false }, // Remove grade vertical (igual a imagem)
                    ticks: { 
                        color: '#adb5bd',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        };

        // 4. Renderizar
        new Chart(ctx, {
            type: 'line', // MUDAMOS DE BAR PARA LINE
            data: areaChartData,
            options: chartOptions
        });
    });
</script>
@stop
