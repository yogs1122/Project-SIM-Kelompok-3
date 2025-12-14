<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('saldo_pribadi', 15, 2)->default(0)->after('remember_token');
            $table->decimal('saldo_toko', 15, 2)->default(0)->after('saldo_pribadi');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['saldo_pribadi', 'saldo_toko']);
        });
    }
};
