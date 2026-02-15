<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produtos = [
            // Cortes
            ['name' => 'Corte Máquina', 'price' => 20.00, 'category' => 'Cortes'],
            ['name' => 'Corte Tesoura', 'price' => 25.00, 'category' => 'Cortes'],
            ['name' => 'Corte Maq. Tes', 'price' => 25.00, 'category' => 'Cortes'],
            ['name' => 'Corte Navalhado', 'price' => 30.00, 'category' => 'Cortes'],
            ['name' => 'Corte Progressiva', 'price' => 70.00, 'category' => 'Cortes'],

            // Barba e Acabamentos
            ['name' => 'Barba', 'price' => 20.00, 'category' => 'Cortes'],
            ['name' => 'Bigode', 'price' => 5.00, 'category' => 'Cortes'],
            ['name' => 'Cavanhaque', 'price' => 10.00, 'category' => 'Cortes'],
            ['name' => 'Pezinho', 'price' => 10.00, 'category' => 'Cortes'],
            ['name' => 'Sobrancelha', 'price' => 10.00, 'category' => 'Cortes'],

            // Químicas / Estilo
            ['name' => 'Graxa', 'price' => 20.00, 'category' => 'Cortes'],
            ['name' => 'Pigmentação', 'price' => 20.00, 'category' => 'Cortes'],
            ['name' => 'Luzes', 'price' => 80.00, 'category' => 'Cortes'],
            ['name' => 'Platinado', 'price' => 120.00, 'category' => 'Cortes'],

            // Produtos Físicos
            ['name' => 'Minoxidil', 'price' => 80.00, 'category' => 'Produtos'],
            ['name' => 'Leave In', 'price' => 30.00, 'category' => 'Produtos'],
            ['name' => 'Shampoo Anti Caspa', 'price' => 30.00, 'category' => 'Produtos'],
            ['name' => 'Óleo Para Barba', 'price' => 30.00, 'category' => 'Produtos'],
            ['name' => 'Balm', 'price' => 30.00, 'category' => 'Produtos'],
            ['name' => 'Pomada', 'price' => 25.00, 'category' => 'Produtos'],

            //bebidas
            ['name' => 'Coca-cola', 'price' => 5.00, 'category' => 'Bebidas'],
            ['name' => 'Monster', 'price' => 12.00, 'category' => 'Bebidas'],
            ['name' => 'Cerveja', 'price' => 3.50, 'category' => 'Bebidas'],
        ];

        foreach ($produtos as $produto) {
            // O firstOrCreate evita duplicar caso você rode o comando mais de uma vez sem querer
            Produto::firstOrCreate(
                ['name' => $produto['name']], 
                $produto
            );
        }
    }
}