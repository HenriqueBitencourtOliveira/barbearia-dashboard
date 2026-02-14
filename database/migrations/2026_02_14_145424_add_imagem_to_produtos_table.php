<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            // Adiciona a coluna 'imagem', pode ser nula caso não tenha foto
            $table->string('imagem')->nullable()->after('categoria');
        });
    }

    public function down()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn('imagem');
        });
    }
};
