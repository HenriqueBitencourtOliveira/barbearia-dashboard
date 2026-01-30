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
        var dailyCtx = document.getElementById('dailyChart').getContext('2d');

        new Chart(dailyCtx, {
            type: 'line', // Mudei para LINHA pra ficar estiloso o fluxo do dia (pode ser bar tbm)
            data: {
                labels: {!! json_encode($dataDaily['labels']) !!},
                datasets: [{
                    label: 'Vendas Hoje',
                    backgroundColor: 'rgba(52,58,64, 0.5)', // Cor "Info" do AdminLTE
                    borderColor: 'rgba(52,58,64, 1)', // Amarelo forte
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    fill: true, // Preenche embaixo da linha
                    data: {!! json_encode($dataDaily['data']) !!}
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
