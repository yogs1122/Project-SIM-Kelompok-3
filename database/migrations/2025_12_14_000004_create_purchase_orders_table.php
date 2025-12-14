<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->string('status')->default('pending'); // pending, paid, completed, cancelled
            $table->string('proof_path')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
};
