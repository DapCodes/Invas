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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id');
            $table->string('serial_number')->unique();
            $table->decimal('initial_quantity', 14, 2)->default(1.00);
            $table->decimal('current_quantity', 14, 2)->default(1.00);
            $table->unsignedBigInteger('satuan_id')->nullable();
            $table->string('status', 30)->default('available');
            $table->unsignedBigInteger('ruangan_id')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
            $table->foreign('satuan_id')->references('id')->on('units')->onDelete('set null');
            $table->foreign('ruangan_id')->references('id')->on('ruangans')->onDelete('set null');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');

            $table->index('status');
            $table->index(['barang_id', 'status']);
            $table->index(['barang_id', 'ruangan_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_items');
    }
};
