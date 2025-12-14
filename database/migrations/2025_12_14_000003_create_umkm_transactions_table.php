<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('umkm_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // e.g., transfer, internal_transfer, withdraw, sale
            $table->string('source')->nullable(); // saldo_pribadi / saldo_toko / external
            $table->string('target')->nullable(); // user:id or bank account
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->text('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('umkm_transactions');
    }
};
