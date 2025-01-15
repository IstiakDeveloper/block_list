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
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->date('transaction_date');

            // Receiving Section
            $table->integer('receive_quantity')->default(0);
            $table->integer('receipt_from_number')->nullable(); // Starting number of received receipts
            $table->integer('receipt_to_number')->nullable();   // Ending number of received receipts
            $table->integer('total_cumulative_quantity')->default(0); // Running total of all receipts
            $table->string('received_by')->nullable();

            // Distribution Section (in the same row)
            $table->string('given_to')->nullable();
            $table->string('pin_number')->nullable();
            $table->integer('given_from_number')->nullable(); // Starting number of distributed receipts
            $table->integer('given_to_number')->nullable();   // Ending number of distributed receipts
            $table->string('receipt_book_number')->nullable();
            $table->integer('given_quantity')->default(0);
            $table->integer('available_receipts')->default(0); // Remaining available receipts

            // Timestamps for record keeping
            $table->timestamps();

            // Index for better query performance
            $table->index(['branch_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
