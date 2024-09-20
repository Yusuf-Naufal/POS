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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('resi')->unique();
            $table->string('nama_pemesan');
            $table->string('no_telp');
            $table->foreignId('id_outlet')->constrained('outlet');
            $table->date('tanggal');      
            $table->integer('total_qty');
            $table->decimal('total_belanja', 10, 2);
            $table->time('jam_mengambil');
            $table->string('pembayaran');
            $table->string('status')->default('Pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
