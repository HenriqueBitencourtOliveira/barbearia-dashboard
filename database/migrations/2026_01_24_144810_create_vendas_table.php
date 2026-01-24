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
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            
            $table->string('description');
            $table->decimal('amount', 10, 2); // 'amount' é mais comum que 'value_money'
            $table->string('payment_method'); // 'method' é mais usado que 'form'
            $table->string('payment_id')->nullable()->index();
            $table->string('status')->default('completed');
            $table->dateTime('sold_at'); // ou sale_date
            $table->string('barber')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
