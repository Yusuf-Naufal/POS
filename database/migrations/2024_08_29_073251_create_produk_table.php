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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->string('sku');
            $table->unsignedBigInteger('id_kategori');
            $table->unsignedBigInteger('id_unit');
            $table->unsignedBigInteger('id_outlet');
            $table->string('stok_awal')->nullable($value = true);
            $table->string('stok_minimum')->nullable($value = true);
            $table->string('harga_jual');
            $table->string('harga_modal')->nullable($value = true);
            $table->string('status');
            $table->string('foto')->nullable($value = true);
            $table->string('catatan')->nullable($value = true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
