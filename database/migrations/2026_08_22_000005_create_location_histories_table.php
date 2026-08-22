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
        Schema::create('location_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_item_id');
            $table->unsignedBigInteger('from_ruangan_id')->nullable();
            $table->unsignedBigInteger('to_ruangan_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->dateTime('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('cascade');
            $table->foreign('from_ruangan_id')->references('id')->on('ruangans')->onDelete('set null');
            $table->foreign('to_ruangan_id')->references('id')->on('ruangans')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['inventory_item_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_histories');
    }
};
