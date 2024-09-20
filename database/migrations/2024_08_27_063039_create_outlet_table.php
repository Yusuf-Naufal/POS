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
        Schema::create('outlet', function (Blueprint $table) {
            $table->id();
            $table->string('nama_outlet');
            $table->string('pemilik');
            $table->string('no_telp');
            $table->string('pin');
            $table->string('email');
            $table->string('alamat');
            $table->string('instagram')->nullable($value = true);
            $table->string('facebook')->nullable($value = true);
            $table->string('tiktok')->nullable($value = true);
            $table->string('status')->default('Aktif');
            $table->string('foto')->nullable($value = true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet');
    }
};
