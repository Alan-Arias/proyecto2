<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transacciones_pago_facil', function (Blueprint $table) {
            $table->string('pagofacil_transaction_id')->nullable()->after('codigo_transaccion');
            $table->unsignedBigInteger('payment_method_id_pago_facil')->nullable()->after('pagofacil_transaction_id');
            $table->string('checkout_url')->nullable()->after('url_qr');
            $table->string('deep_link')->nullable()->after('checkout_url');
            $table->string('qr_content_url')->nullable()->after('deep_link');
            $table->string('universal_url')->nullable()->after('qr_content_url');
        });
    }

    public function down(): void
    {
        Schema::table('transacciones_pago_facil', function (Blueprint $table) {
            $table->dropColumn([
                'pagofacil_transaction_id',
                'payment_method_id_pago_facil',
                'checkout_url',
                'deep_link',
                'qr_content_url',
                'universal_url',
            ]);
        });
    }
};
