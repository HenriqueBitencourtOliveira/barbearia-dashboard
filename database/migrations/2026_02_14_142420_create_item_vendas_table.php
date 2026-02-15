<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_vendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venda_id')->constrained('vendas')->onDelete('cascade');
            
            $table->string('name'); // Antes era 'nome'
            $table->decimal('unit_price', 10, 2); // Antes era 'preco_unitario'
            $table->integer('quantity')->default(1); // Antes era 'quantidade'
            $table->string('category'); // Antes era 'categoria'
            $table->string('barber');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_vendas');
    }
};