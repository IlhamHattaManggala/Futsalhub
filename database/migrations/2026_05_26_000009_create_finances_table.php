<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->string('type'); // Pemasukan, Pengeluaran
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('description');
            $table->string('category'); // Iuran Pemain, Sewa Lapangan, Peralatan, dll
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finances');
    }
};
