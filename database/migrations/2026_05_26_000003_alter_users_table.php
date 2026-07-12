<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('team_id')->after('id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->foreignId('role_id')->after('team_id')->constrained('roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['team_id']);
            $table->dropColumn(['role_id', 'team_id']);
        });
    }
};
