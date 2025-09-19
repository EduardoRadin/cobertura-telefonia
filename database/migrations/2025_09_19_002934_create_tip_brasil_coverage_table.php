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
        Schema::create('tip_brasil_coverage', function (Blueprint $table) {
            $table->id();
            $table->string('municipio');
            $table->string('cnl_al', 10);
            $table->string('sigla_estado', 2);
            $table->string('cn', 2);
            $table->string('cnl', 10);
            $table->string('maestro', 3);
            $table->string('area_local');
            $table->timestamps();
            
            $table->index(['sigla_estado', 'municipio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tip_brasil_coverage');
    }
};
