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
        Schema::create('pengajuan_outlet', function (Blueprint $table) {
            $table->id();
            $table->string('nama_outlet');
            $table->unsignedBigInteger('id_pemilik');
            $table->string('no_telp');
            $table->string('pin');
            $table->string('email');
            $table->string('alamat');
            $table->string('instagram')->nullable($value = true);
            $table->string('facebook')->nullable($value = true);
            $table->string('tiktok')->nullable($value = true);
            $table->string('status')->default('Pending');
            $table->string('foto')->nullable($value = true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_outlet');
    }
};
