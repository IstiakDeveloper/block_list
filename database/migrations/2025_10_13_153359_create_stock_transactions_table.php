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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
            $table->enum('transaction_type', ['stock_in', 'distribute_to_branch', 'distribute_to_person']);
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->date('transaction_date');
            $table->integer('book_from')->comment('Book number from');
            $table->integer('book_to')->comment('Book number to');
            $table->integer('receipt_from')->comment('Receipt number from');
            $table->integer('receipt_to')->comment('Receipt number to');
            $table->integer('total_books')->comment('Total books in transaction');
            $table->integer('total_receipts')->comment('Total receipts in transaction');
            $table->string('given_to')->nullable()->comment('Person name if distributed');
            $table->string('pin_number')->nullable()->comment('Person PIN if distributed');
            $table->string('received_by')->nullable()->comment('Who received');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['lot_id', 'transaction_date']);
            $table->index(['branch_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
