<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id');
            $table->unsignedBigInteger('inventory_item_id')->nullable();
            $table->string('type', 30); // in, out, borrow, return, transfer, adjustment, initial
            $table->decimal('quantity', 14, 2);
            $table->decimal('quantity_before', 14, 2);
            $table->decimal('quantity_after', 14, 2);
            $table->string('reference_type')->nullable(); // barang_masuk, barang_keluar, peminjaman, pengembalian, adjustment, transfer
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedBigInteger('ruangan_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->dateTime('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('set null');
            $table->foreign('ruangan_id')->references('id')->on('ruangans')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->index('type');
            $table->index('tanggal');
            $table->index(['barang_id', 'tanggal']);
            $table->index(['inventory_item_id', 'tanggal']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
};
