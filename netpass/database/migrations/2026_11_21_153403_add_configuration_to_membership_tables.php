<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_categories', function (Blueprint $table) {
            $table->text('description')->nullable();
        });
        Schema::table('membership_levels', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->unsignedInteger('validity_days')->nullable();
            $table->text('benefits')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('membership_levels', function (Blueprint $table) {
            $table->dropColumn(['description', 'fee', 'validity_days', 'benefits']);
        });
        Schema::table('membership_categories', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
