<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Plano;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteCorteTest extends TestCase
{
    use RefreshDatabase;

    public function test_registra_corte_e_atualiza_o_saldo_do_cliente(): void
    {
        $usuario = User::factory()->create();
        $plano = Plano::create([
            'name' => 'Plano 4 cortes',
            'price' => 100,
            'duration_in_days' => 30,
            'cuts_included' => 4,
            'is_active' => true,
        ]);
        $cliente = Cliente::create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'plano_id' => $plano->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($usuario)
            ->post(route('clientes.cortes.store', $cliente));

        $response->assertRedirect();
        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'cuts_used' => 1,
        ]);
        $this->assertDatabaseHas('cliente_cortes', [
            'cliente_id' => $cliente->id,
            'user_id' => $usuario->id,
        ]);
    }

    public function test_nao_registra_corte_quando_a_franquia_foi_esgotada(): void
    {
        $usuario = User::factory()->create();
        $plano = Plano::create([
            'name' => 'Plano 1 corte',
            'price' => 50,
            'duration_in_days' => 30,
            'cuts_included' => 1,
            'is_active' => true,
        ]);
        $cliente = Cliente::create([
            'name' => 'Cliente Teste',
            'email' => 'cliente-esgotado@example.com',
            'plano_id' => $plano->id,
            'cuts_used' => 1,
            'status' => 'active',
        ]);

        $response = $this->actingAs($usuario)
            ->post(route('clientes.cortes.store', $cliente));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'cuts_used' => 1,
        ]);
        $this->assertDatabaseCount('cliente_cortes', 0);
    }
}