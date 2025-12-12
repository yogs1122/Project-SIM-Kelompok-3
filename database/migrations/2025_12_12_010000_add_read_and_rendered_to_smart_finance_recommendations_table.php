<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('smart_finance_recommendations', function (Blueprint $table) {
            $table->text('rendered_message')->nullable()->after('message');
            $table->boolean('is_read')->default(false)->after('rendered_message');
        });
    }

    public function down()
    {
        Schema::table('smart_finance_recommendations', function (Blueprint $table) {
            $table->dropColumn(['rendered_message','is_read']);
        });
    }
};
