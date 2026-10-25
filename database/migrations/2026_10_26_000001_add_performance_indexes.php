<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan index pada kolom yang sering dipakai pencarian, filter,
 * dan agregasi dashboard (status, tanggal transaksi).
 */
class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        foreach (['sales' => 'sold_at', 'purchases' => 'purchased_at'] as $table => $dateColumn) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $this->addIndexSafely($table, $dateColumn, $dateColumn . '_index');
            $this->addIndexSafely($table, 'status', $table . '_status_index');
        }
    }

    /**
     * Tidak memulihkan index pada rollback agar tidak mengganggu struktur
     * yang mungkin sudah dibuat oleh migration lain.
     */
    public function down()
    {
        foreach (['sales' => 'sold_at', 'purchases' => 'purchased_at'] as $table => $dateColumn) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if ($this->indexExists($table, $dateColumn . '_index')) {
                Schema::table($table, fn ($t) => $t->dropIndex($dateColumn . '_index'));
            }
            if ($this->indexExists($table, $table . '_status_index')) {
                Schema::table($table, fn ($t) => $t->dropIndex($table . '_status_index'));
            }
        }
    }

    private function addIndexSafely(string $table, string $column, string $name): void
    {
        if (! $this->indexExists($table, $name)) {
            Schema::table($table, function (Illuminate\Database\Schema\Blueprint $t) use ($column, $name) {
                $t->index($column, $name);
            });
        }
    }

    private function indexExists(string $table, string $name): bool
    {
        return count(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name])) > 0;
    }
}
