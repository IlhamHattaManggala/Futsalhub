<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed initial platform defaults
        DB::table('settings')->insert([
            [
                'key' => 'web_logo',
                'value' => 'images/logo.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'web_favicon',
                'value' => 'favicon.ico',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'web_name',
                'value' => 'FutsalHub',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'web_description',
                'value' => 'Sistem manajemen tim futsal modern yang terintegrasi dengan absensi, kas keuangan, taktik board, dan gerbang pembayaran premium.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'web_keywords',
                'value' => 'futsal, tim futsal, manajemen futsal, tactical board, kas futsal, absensi futsal',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'tripay_api_key',
                'value' => '',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'tripay_private_key',
                'value' => '',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'tripay_merchant_code',
                'value' => '',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'tripay_merchant_name',
                'value' => 'FutsalHub Sandbox',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'tripay_mode',
                'value' => 'sandbox',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'platform_fee',
                'value' => '5000',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
