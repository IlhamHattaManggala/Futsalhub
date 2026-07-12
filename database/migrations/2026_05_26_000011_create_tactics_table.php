<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tactics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('coach_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('formation')->default('1-2-1');
            $table->json('canvas_data')->nullable(); // Stores position of tokens, drawings, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tactics');
    }
};
