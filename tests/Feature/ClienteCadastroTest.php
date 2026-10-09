<?php

namespace Tests\Feature;

use App\Models\Plano;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteCadastroTest extends TestCase
{
    use RefreshDatabase;

    public function test_cadastra_cliente_vinculando_plano(): void
    {
        $usuario = User::factory()->create();
        $plano = Plano::create([
            'name' => 'Plano 4 cortes',
            'price' => 100,
            'duration_in_days' => 30,
            'cuts_included' => 4,
            'is_active' => true,
        ]);

        $response = $this->actingAs($usuario)
            ->post(route('clientes.store'), [
                'name' => 'Cliente Teste',
                'email' => 'cliente@example.com',
                'plano_id' => $plano->id,
            ]);

        $response->assertRedirect(route('clientes.index'));
        $this->assertDatabaseHas('clientes', [
            'email' => 'cliente@example.com',
            'plano_id' => $plano->id,
        ]);
    }

    public function test_formulario_de_novo_cliente_exibe_selecao_de_plano(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('clientes.index'));

        $response->assertOk();
        $response->assertSee('id="new-client-plan"', false);
    }
}
