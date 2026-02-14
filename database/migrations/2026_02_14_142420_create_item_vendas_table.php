<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_vendas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('venda_id')->constrained('vendas')->onDelete('cascade');
        $table->string('nome');
        $table->decimal('preco_unitario', 10, 2); 
        $table->integer('quantidade')->default(1);
        $table->string('categoria'); 
        $table->string('barber');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_vendas');
    }
};
