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
        Schema::create('ligue_ai_coverage', function (Blueprint $table) {
            $table->id();
            $table->string('uf', 2);
            $table->string('cn', 2);
            $table->string('municipio');
            $table->timestamps();
            
            $table->index(['uf', 'municipio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligue_ai_coverage');
    }
};
