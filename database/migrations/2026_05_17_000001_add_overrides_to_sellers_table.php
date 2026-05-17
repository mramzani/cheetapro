<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->unsignedBigInteger('price_per_gb')->nullable()->after('is_active');
            $table->unsignedBigInteger('xui_inbound_id')->nullable()->after('price_per_gb');
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['price_per_gb', 'xui_inbound_id']);
        });
    }
};
