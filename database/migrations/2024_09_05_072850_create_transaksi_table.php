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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();  
            $table->string('resi'); 
            $table->date('tanggal_transaksi'); 
            $table->unsignedBigInteger('total_qty'); 
            $table->decimal('total_belanja', 15, 2); 
            $table->unsignedBigInteger('id_outlet');
            $table->string('status')->default('Pending'); 
            $table->string('catatan')->nullable($value = true) ;

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_outlet')->references('id')->on('outlet')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
