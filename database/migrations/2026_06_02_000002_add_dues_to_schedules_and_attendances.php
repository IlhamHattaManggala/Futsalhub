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
        Schema::table('schedules', function (Blueprint $table) {
            $table->decimal('dues_amount', 12, 2)->default(0.00)->after('opponent');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_dues_paid')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('dues_amount');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('is_dues_paid');
        });
    }
};
