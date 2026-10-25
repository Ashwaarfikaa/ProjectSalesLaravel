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

        // Hapus TABLES LEGACY versi pertama (aplikasi "Donuts") saja.
        // Tabel products/customers dipakai ulang oleh skema terintegrasi,
        // sehingga tidak boleh kita drop di sini (migration pembentuknya
        // sudah punya guard hasTable sendiri).
        foreach (['transaction_items', 'transaction_details', 'transactions'] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        // Tabel lama tidak dipulihkan.
    }
}