<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venda;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = date('Y');
        $previousYear = date('Y') - 1;
        $barberSelect = $request->input('barber');

        $currentSales = $this->getMonthlySales($currentYear, $barberSelect);
        $previousSales = $this->getMonthlySales($previousYear, $barberSelect);

        $dataWeekly = $this->getWeeklySales($request);
        $dataDaily = $this->getDailySales($request);

        // Busca barbeiros distintos das vendas e das assinaturas de clientes
        $barbeirosVendas = Venda::whereNotNull('barber')->pluck('barber')->toArray();
        $barbeirosClientes = Cliente::whereNotNull('barber')->pluck('barber')->toArray();
        
        $barbeiros = collect(array_merge($barbeirosVendas, $barbeirosClientes))
            ->unique()
            ->sort()
            ->values();

        return view('dashboard.index', compact('currentSales', 'previousSales', 'barbeiros', 'barberSelect', 'dataWeekly', 'dataDaily'));
    }

    /**
     * Retorna as vendas por mês para o ano especificado.
     * Caso seja informado o barbeiro, retorna as vendas somente para aquele barbeiro.
     *
     * @param int $year Ano para o qual se deseja obter as vendas.
     * @param string|null $barber Opcional, barbeiro para o qual se deseja obter as vendas.
     * @return array Um array com as vendas por mês.
     */
    private function getMonthlySales($year, $barber = null)
    {
        $arraySales = array_fill(0, 12, 0);

        // 1. Soma das Vendas normais
        $queryVendas = Venda::selectRaw('MONTH(sold_at) as mes, SUM(amount) as total')
            ->whereYear('sold_at', $year)
            ->where(function ($query) {
                $query->where('status', 'processed')
                      ->orWhere('status', 'completed');
            });

        if ($barber) {
            $queryVendas->where('barber', $barber);
        }

        $dataVendas = $queryVendas->groupBy('mes')->orderBy('mes')->get();

        foreach ($dataVendas as $sale) {
            $arraySales[$sale->mes - 1] += $sale->total;
        }

        return $arraySales;
    }


    /**
     * Retorna um array com as vendas semanais, com a chave 'labels' para os labels do gráfico e 'data' para os dados do gráfico.
     * As vendas são somadas por dia, desde 6 dias atrás até o dia atual.
     * Se tiver barbeiro, a soma será feita apenas com as vendas do barbeiro informado.
     * @param Request $request
     * @return array
     */
    private function getWeeklySales(Request $request)
    {
        $vendasTotais = [];
        $labels = [];
        $barberSelect = $request->input('barber');

        for ($i = 6; $i >= 0; $i--) {
            $data = now()->subDays($i);
            $labels[] = $data->format('d/m');

            // 1. Soma Vendas
            $queryVendas = Venda::whereDate('sold_at', $data->toDateString())
                ->where(function ($query) {
                    $query->where('status', 'processed')
                          ->orWhere('status', 'completed');
                });

            if ($barberSelect) {
                $queryVendas->where('barber', $barberSelect);
            }

            $somaVendas = $queryVendas->sum('amount');

            $vendasTotais[] = $somaVendas;
        }

        return ['labels' => $labels, 'data' => $vendasTotais];
    }

    /**
     * Retorna um array com as vendas diárias do dia atual, com a chave 'labels' para os labels do gráfico e 'data' para os dados do gráfico.
     * As vendas são somadas por hora, desde as 7h até as 23h.
     * Se tiver barbeiro, a soma será feita apenas com as vendas do barbeiro informado.
     * @param Request $request
     * @return array
     */
    private function getDailySales(Request $request)
    {
        $salesTotais = [];
        $labels = [];
        $barberSelect = $request->input('barber');
        $date = now(); 

        for ($i = 7; $i <= 23; $i++) {
            $horaInicio = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00:00';
            $horaFim    = str_pad($i, 2, '0', STR_PAD_LEFT) . ':59:59';

            $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';

            // 1. Soma Vendas
            $queryVendas = Venda::whereDate('sold_at', $date->toDateString())
                ->whereTime('sold_at', '>=', $horaInicio)
                ->whereTime('sold_at', '<=', $horaFim)
                ->where(function ($query) {
                    $query->where('status', 'processed')
                          ->orWhere('status', 'completed');
                });

            if ($barberSelect) {
                $queryVendas->where('barber', $barberSelect);
            }

            $somaVendas = $queryVendas->sum('amount');

            $salesTotais[] = $somaVendas;
        }

        return ['labels' => $labels, 'data' => $salesTotais];
    }
}
