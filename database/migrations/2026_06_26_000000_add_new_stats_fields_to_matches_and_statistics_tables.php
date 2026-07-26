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
        Schema::table('matches', function (Blueprint $table) {
            $table->integer('possession_team')->nullable()->after('score_opponent');
            $table->integer('possession_opponent')->nullable()->after('possession_team');
            $table->integer('shoot_on_target_team')->nullable()->after('possession_opponent');
            $table->integer('shoot_on_target_opponent')->nullable()->after('shoot_on_target_team');
            $table->integer('shoot_off_target_team')->nullable()->after('shoot_on_target_opponent');
            $table->integer('shoot_off_target_opponent')->nullable()->after('shoot_off_target_team');
        });

        Schema::table('statistics', function (Blueprint $table) {
            $table->integer('clearance')->default(0)->after('minutes_played');
            $table->integer('save')->default(0)->after('clearance');
            $table->integer('shoot_on_target')->default(0)->after('save');
            $table->integer('shoot_off_target')->default(0)->after('shoot_on_target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn([
                'possession_team',
                'possession_opponent',
                'shoot_on_target_team',
                'shoot_on_target_opponent',
                'shoot_off_target_team',
                'shoot_off_target_opponent'
            ]);
        });

        Schema::table('statistics', function (Blueprint $table) {
            $table->dropColumn([
                'clearance',
                'save',
                'shoot_on_target',
                'shoot_off_target'
            ]);
        });
    }
};
