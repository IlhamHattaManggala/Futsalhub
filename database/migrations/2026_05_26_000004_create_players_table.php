<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->integer('number');
            $table->string('position'); // Anchor, Flank, Pivot, Goalkeeper
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('height')->nullable(); // in cm
            $table->integer('weight')->nullable(); // in kg
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
