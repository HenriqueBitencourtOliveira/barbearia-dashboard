<div class="card card-gray-dark">
    <div class="card-header">
        <h3 class="card-title">Vendas: Últimos 7 Dias</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
    </div>
    <div class="card-body">
        <div class="chart">
            <canvas id="weeklyChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var weeklyCanvas = document.getElementById('weeklyChart');
        var weeklyCtx = weeklyCanvas.getContext('2d');

        // Gradient vertical usando as cores do tema (roxo -> azul)
        var gradient = weeklyCtx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(124,58,237,0.95)'); // #7c3aed
        gradient.addColorStop(1, 'rgba(14,165,233,0.9)');   // #0ea5e9

        new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                // Labels vindas do PHP (Ex: 25/01, 26/01...)
                labels: {!! json_encode($dataWeekly['labels']) !!},
                datasets: [{
                    label: 'Faturamento (R$)',
                    backgroundColor: gradient,
                    borderColor: 'rgba(124,58,237,1)',
                    borderWidth: 0,
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 36,
                    data: {!! json_encode($dataWeekly['data']) !!}
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
                                size: 12
                            }
                        },
                        grid: { color: 'rgba(30,41,59,0.5)' }
                    }
                }
            }
        });
    });
</script>