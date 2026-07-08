<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transacciones_pago_facil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->unique()->constrained('payments')->cascadeOnDelete();
            $table->foreignId('inscripcion_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('estudiante_id')->constrained('students')->cascadeOnDelete();
            $table->string('codigo_transaccion')->unique();
            $table->text('codigo_qr')->nullable();
            $table->text('url_qr')->nullable();
            $table->decimal('monto', 10, 2);
            $table->string('moneda', 10)->default('BOB');
            $table->string('concepto');
            $table->string('estado')->default('generado');
            $table->timestamp('fecha_generacion')->nullable();
            $table->timestamp('fecha_vencimiento')->nullable();
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->json('respuesta_pago_facil')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacciones_pago_facil');
    }
};
