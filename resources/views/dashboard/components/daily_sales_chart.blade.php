<div class="card card-gray-dark">
    <div class="card-header">
        <h3 class="card-title">Hoje: Vendas por Hora</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
    </div>
    <div class="card-body">
        <div class="chart">
            <canvas id="dailyChart"
                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var dailyCanvas = document.getElementById('dailyChart');
        var dailyCtx = dailyCanvas.getContext('2d');

        // Gradient para preenchimento suave da linha
        var gradientDaily = dailyCtx.createLinearGradient(0, 0, 0, 250);
        gradientDaily.addColorStop(0, 'rgba(124,58,237,0.35)'); // topo mais opaco
        gradientDaily.addColorStop(1, 'rgba(14,165,233,0.0)');   // base transparente

        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dataDaily['labels']) !!},
                datasets: [{
                    label: 'Vendas Hoje',
                    data: {!! json_encode($dataDaily['data']) !!},
                    borderColor: '#7c3aed',
                    backgroundColor: gradientDaily,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        labels: { 
                            color: '#e2e8f0',
                            font: {
                                size: 13,
                                weight: 'bold'
                            }
                        } 
                    },
                    tooltip: { 
                        backgroundColor: '#0f172a', 
                        titleColor: '#e2e8f0', 
                        bodyColor: '#cbd5e1',
                        titleFont: {
                            size: 13,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 14
                        }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    x: {
                        ticks: { 
                            color: '#cbd5e1',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            color: '#cbd5e1',
                            font: {
                                size: 14
                            }
                        },
                        grid: { color: 'rgba(30,41,59,0.5)' }
                    }
                }
            }
        });
    });
</script>
