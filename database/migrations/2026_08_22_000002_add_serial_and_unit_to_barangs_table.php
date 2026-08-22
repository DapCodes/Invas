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
        Schema::table('barangs', function (Blueprint $table) {
            $table->boolean('has_serial_number')->default(false)->after('serial_number');
            $table->unsignedBigInteger('satuan_id')->nullable()->after('stok');
            $table->text('deskripsi')->nullable()->after('foto');
            $table->boolean('is_active')->default(true)->after('has_serial_number');

            $table->foreign('satuan_id')->references('id')->on('units')->onDelete('set null');
            $table->index(['nama', 'merek']);
            $table->index('has_serial_number');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropForeign(['satuan_id']);
            $table->dropIndex(['nama', 'merek']);
            $table->dropIndex(['has_serial_number']);
            $table->dropIndex(['is_active']);
            $table->dropColumn(['has_serial_number', 'satuan_id', 'deskripsi', 'is_active']);
        });
    }
};
