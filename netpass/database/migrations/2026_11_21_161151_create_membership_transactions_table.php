<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 40)->nullable()->unique();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->foreignId('membership_level_id')->constrained()->restrictOnDelete();
            $table->string('level_name');
            $table->unsignedInteger('validity_days');
            $table->string('transaction_type', 20);
            $table->decimal('amount', 10, 2);
            $table->string('status', 20)->default('Pending');
            $table->dateTime('transaction_date');
            $table->date('membership_start_date')->nullable();
            $table->date('membership_end_date')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['member_id', 'transaction_type', 'status']);
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_transactions');
    }
};
