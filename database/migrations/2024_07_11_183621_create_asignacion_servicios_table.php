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
        Schema::create('asignacion_servicios', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_asignacion');
            $table->string('estado', 50);
            $table->unsignedBigInteger('id_trabajador');
            $table->unsignedBigInteger('id_servicio');
            $table->foreign('id_trabajador')->references('id')->on('trabajadors')->onDelete('cascade');
            $table->foreign('id_servicio')->references('id')->on('servicios')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_servicios');
    }
};
