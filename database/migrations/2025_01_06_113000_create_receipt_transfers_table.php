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
        Schema::create('receipt_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreignId('to_branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who made the transfer
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->timestamp('transfer_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_transfers');
    }
};
