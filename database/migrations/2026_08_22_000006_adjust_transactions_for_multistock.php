<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // 1. Alter existing columns to DECIMAL(14, 2) and make ruangan_id nullable
        DB::statement("ALTER TABLE `barangs` MODIFY `stok` DECIMAL(14, 2) NOT NULL DEFAULT 0.00");
        DB::statement("ALTER TABLE `barang_ruangans` MODIFY `stok` DECIMAL(14, 2) NOT NULL DEFAULT 0.00");
        DB::statement("ALTER TABLE `barang_masuks` MODIFY `jumlah` DECIMAL(14, 2) NOT NULL");
        DB::statement("ALTER TABLE `barang_masuks` MODIFY `ruangan_id` BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE `barang_keluars` MODIFY `jumlah` DECIMAL(14, 2) NOT NULL");
        DB::statement("ALTER TABLE `barang_keluars` MODIFY `ruangan_id` BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE `peminjamans` MODIFY `jumlah` DECIMAL(14, 2) NOT NULL");
        DB::statement("ALTER TABLE `peminjamans` MODIFY `ruangan_id` BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE `pengembalians` MODIFY `jumlah` DECIMAL(14, 2) NOT NULL");
        DB::statement("ALTER TABLE `pengembalians` MODIFY `ruangan_id` BIGINT UNSIGNED NULL");

        // 2. Add columns to barang_masuks
        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_item_id')->nullable()->after('id_barang');
            $table->unsignedBigInteger('satuan_id')->nullable()->after('jumlah');

            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('set null');
            $table->foreign('satuan_id')->references('id')->on('units')->onDelete('set null');
        });

        // 3. Add columns to barang_keluars
        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_item_id')->nullable()->after('id_barang');
            $table->unsignedBigInteger('satuan_id')->nullable()->after('jumlah');

            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('set null');
            $table->foreign('satuan_id')->references('id')->on('units')->onDelete('set null');
        });

        // 4. Add columns to peminjamans
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_item_id')->nullable()->after('id_barang');
            $table->unsignedBigInteger('satuan_id')->nullable()->after('jumlah');

            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('set null');
            $table->foreign('satuan_id')->references('id')->on('units')->onDelete('set null');
        });

        // 5. Add columns to pengembalians
        Schema::table('pengembalians', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_item_id')->nullable()->after('id_barang');
            $table->unsignedBigInteger('satuan_id')->nullable()->after('jumlah');
            $table->string('kondisi', 50)->nullable()->after('status');
            $table->decimal('selisih', 14, 2)->default(0.00)->after('jumlah');

            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('set null');
            $table->foreign('satuan_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengembalians', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropForeign(['satuan_id']);
            $table->dropColumn(['inventory_item_id', 'satuan_id', 'kondisi', 'selisih']);
        });

        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropForeign(['satuan_id']);
            $table->dropColumn(['inventory_item_id', 'satuan_id']);
        });

        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropForeign(['satuan_id']);
            $table->dropColumn(['inventory_item_id', 'satuan_id']);
        });

        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropForeign(['satuan_id']);
            $table->dropColumn(['inventory_item_id', 'satuan_id']);
        });

        DB::statement("ALTER TABLE `pengembalians` MODIFY `jumlah` BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE `peminjamans` MODIFY `jumlah` BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE `barang_keluars` MODIFY `jumlah` INT NOT NULL");
        DB::statement("ALTER TABLE `barang_masuks` MODIFY `jumlah` BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE `barang_ruangans` MODIFY `stok` BIGINT UNSIGNED NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE `barangs` MODIFY `stok` BIGINT UNSIGNED NOT NULL DEFAULT 0");
    }
};
