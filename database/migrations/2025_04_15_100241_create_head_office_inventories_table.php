<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('head_office_inventories', function (Blueprint $table) {
            $table->id();
            $table->integer('total_stock')->default(0)->comment('Current available stock at head office');
            $table->integer('total_stock_in')->default(0)->comment('Total stock received from press');
            $table->integer('total_stock_out')->default(0)->comment('Total stock distributed to branches');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('head_office_inventories');
    }
};
