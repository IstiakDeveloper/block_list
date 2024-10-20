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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // User who created the customer
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade'); // Link to the branch
            $table->string('nid_part_1')->nullable(); // First part of NID
            $table->string('nid_part_2')->nullable(); // Second part of NID
            $table->string('name');
            $table->string('name_bn')->nullable(); // Name in Bangla
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('dob')->nullable(); // Date of birth
            $table->string('nid_number')->unique(); // Full NID number
            $table->text('address')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
