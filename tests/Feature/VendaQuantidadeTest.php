<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendaQuantidadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_salva_a_quantidade_informada_para_o_item_da_venda(): void
    {
        $usuario = User::factory()->create();

        $response = $this->actingAs($usuario)->post(route('vendas.store'), [
            'description' => '3x Cerveja',
            'barber' => 'Fellipe',
            'amount' => 30,
            'payment_method' => 'money',
            'sold_at' => now()->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'name' => 'Cerveja',
                    'price' => 10,
                    'category' => 'Bebidas',
                    'quantity' => 3,
                ],
            ],
        ]);

        $response->assertRedirect(route('vendas.index'));
        $this->assertDatabaseHas('item_vendas', [
            'name' => 'Cerveja',
            'unit_price' => 10,
            'quantity' => 3,
            'category' => 'Bebidas',
            'barber' => 'Fellipe',
        ]);
    }

    public function test_rejeita_quantidade_invalida_no_item_da_venda(): void
    {
        $usuario = User::factory()->create();

        $response = $this->actingAs($usuario)->post(route('vendas.store'), [
            'description' => 'Cerveja',
            'barber' => 'Fellipe',
            'amount' => 10,
            'payment_method' => 'money',
            'sold_at' => now()->format('Y-m-d H:i:s'),
            'items' => [
                [
                    'name' => 'Cerveja',
                    'price' => 10,
                    'category' => 'Bebidas',
                    'quantity' => 0,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items.0.quantity');
        $this->assertDatabaseCount('vendas', 0);
    }
}
