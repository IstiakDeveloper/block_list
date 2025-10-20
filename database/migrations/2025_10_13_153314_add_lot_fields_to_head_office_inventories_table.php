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
        Schema::table('head_office_inventories', function (Blueprint $table) {
            $table->foreignId('lot_id')->nullable()->after('id')->constrained('lots')->onDelete('cascade');
            $table->integer('total_books')->default(0)->after('lot_id')->comment('Total books in this lot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('head_office_inventories', function (Blueprint $table) {
            $table->dropForeign(['lot_id']);
            $table->dropColumn(['lot_id', 'total_books']);
        });
    }
};
