<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('membership_transaction_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('method', 20);
            $table->date('payment_date');
            $table->string('reference_number', 100);
            $table->uuid('idempotency_key')->unique();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->unique(['method', 'reference_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
