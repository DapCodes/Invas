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
        Schema::create('peminjaman_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('peminjaman_id');
            $table->unsignedBigInteger('barang_id');
            $table->unsignedBigInteger('inventory_item_id')->nullable();
            $table->decimal('quantity', 14, 2)->default(1.00);
            $table->decimal('returned_quantity', 14, 2)->default(0.00);
            $table->string('status', 30)->default('borrowed'); // borrowed, returned, partially_returned
            $table->timestamps();

            $table->foreign('peminjaman_id')->references('id')->on('peminjamans')->onDelete('cascade');
            $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('set null');
        });

        Schema::create('pengembalian_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengembalian_id');
            $table->unsignedBigInteger('peminjaman_detail_id')->nullable();
            $table->unsignedBigInteger('barang_id');
            $table->unsignedBigInteger('inventory_item_id')->nullable();
            $table->decimal('quantity', 14, 2)->default(1.00);
            $table->decimal('selisih', 14, 2)->default(0.00);
            $table->string('kondisi', 50)->default('Baik'); // Baik, Rusak, Hilang, Sebagian Rusak, Tidak Lengkap
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('pengembalian_id')->references('id')->on('pengembalians')->onDelete('cascade');
            $table->foreign('peminjaman_detail_id')->references('id')->on('peminjaman_details')->onDelete('set null');
            $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengembalian_details');
        Schema::dropIfExists('peminjaman_details');
    }
};
