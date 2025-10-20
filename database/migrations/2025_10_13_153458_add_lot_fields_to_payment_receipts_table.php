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
        Schema::table('payment_receipts', function (Blueprint $table) {
            $table->foreignId('stock_transaction_id')->nullable()->after('id')->constrained('stock_transactions')->onDelete('cascade');
            $table->foreignId('lot_id')->nullable()->after('stock_transaction_id')->constrained('lots')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('payment_receipts', function (Blueprint $table) {
            $table->dropForeign(['stock_transaction_id']);
            $table->dropForeign(['lot_id']);
            $table->dropColumn(['stock_transaction_id', 'lot_id']);
        });
    }
};
