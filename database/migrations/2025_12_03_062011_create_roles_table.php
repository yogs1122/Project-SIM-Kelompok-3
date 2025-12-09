<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, user, umkm
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 2. Tabel pivot user_roles
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Insert default roles
        DB::table('roles')->insert([
            ['name' => 'admin', 'description' => 'Administrator System'],
            ['name' => 'user', 'description' => 'Regular User'],
            ['name' => 'umkm', 'description' => 'UMKM Merchant'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
    }
};