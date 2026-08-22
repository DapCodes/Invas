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
        if (Schema::hasColumn('users', 'status_user')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('status_user');
            });
        }

        if (Schema::hasColumn('barangs', 'status_barang')) {
            Schema::table('barangs', function (Blueprint $table) {
                $table->dropColumn('status_barang');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('users', 'status_user')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('status_user')->nullable();
            });
        }

        if (!Schema::hasColumn('barangs', 'status_barang')) {
            Schema::table('barangs', function (Blueprint $table) {
                $table->string('status_barang')->nullable();
            });
        }
    }
};
