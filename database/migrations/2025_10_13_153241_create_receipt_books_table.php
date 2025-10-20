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
        Schema::create('receipt_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
            $table->integer('book_number')->comment('Individual book number like 665, 666');
            $table->integer('from_number')->comment('Starting receipt number like 66501');
            $table->integer('to_number')->comment('Ending receipt number like 66600');
            $table->enum('status', ['available', 'distributed', 'used'])->default('available');
            $table->string('location_type')->default('head_office')->comment('head_office, branch, person');
            $table->string('location_id')->nullable()->comment('branch_id or person identifier');
            $table->foreignId('parent_transaction_id')->nullable()->comment('Which transaction brought this book');
            $table->timestamps();

            // Indexes
            $table->index(['lot_id', 'book_number']);
            $table->index(['status', 'location_type']);
            $table->unique(['lot_id', 'book_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_books');
    }
};
