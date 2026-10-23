<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Menghapus tabel lama (versi sebelum redesign) agar skema baru sesuai
 * spesifikasi UTS dapat dibuat bersih lewat migration.
 */
class DropLegacyInventoryTables extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        foreach (['transaction_items', 'transaction_details', 'transactions', 'customers', 'products'] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        // Tabel lama tidak dipulihkan.
    }
}