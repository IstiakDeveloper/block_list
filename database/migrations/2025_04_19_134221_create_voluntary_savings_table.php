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
        Schema::create('voluntary_savings', function (Blueprint $table) {
            $table->id();
            $table->date('application_date'); // main date

            // Branch info from user.branch_id
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');

            // Somiti & Member info
            $table->string('somiti_name');
            $table->string('somiti_code');
            $table->string('member_name');
            $table->string('member_code');

            // Profit & contact info
            $table->decimal('profit', 10, 2)->nullable();
            $table->string('member_mobile')->nullable();

            // Applicant info
            $table->string('applicant_name');
            $table->string('designation')->nullable();
            $table->string('pin')->nullable();
            $table->string('signature')->nullable(); // Path to signature image/file

            // Status for approval
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });

        // Deposit info (Multiple)
        Schema::create('voluntary_saving_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voluntary_saving_id')->constrained('voluntary_savings')->onDelete('cascade');
            $table->date('deposit_date');
            $table->decimal('deposit_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voluntary_saving_deposits');
        Schema::dropIfExists('voluntary_savings');
    }
};
