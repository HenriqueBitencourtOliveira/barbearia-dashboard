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
        var weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        
        new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                // Labels vindas do PHP (Ex: 25/01, 26/01...)
                labels: {!! json_encode($dataWeekly['labels']) !!},
                datasets: [{
                    label: 'Faturamento (R$)',
                    backgroundColor: '#343a40', 
                    borderColor: 'rgba(23, 162, 184, 1)',
                    data: {!! json_encode($dataWeekly['data']) !!}
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>