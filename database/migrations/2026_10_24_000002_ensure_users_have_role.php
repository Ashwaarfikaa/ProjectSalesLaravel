<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureUsersHaveRole extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 20)->default('staff')->after('password');
            });
        }
    }

    public function down()
    {
        // Kolom role dibiarkan agar tidak merusak data pengguna.
    }
}