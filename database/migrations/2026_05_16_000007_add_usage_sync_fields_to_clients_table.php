<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('used_traffic_bytes')->nullable()->after('total_bytes');
            $table->unsignedBigInteger('remaining_traffic_bytes')->nullable()->after('used_traffic_bytes');
            $table->bigInteger('xui_expiry_time')->nullable()->after('expiry_time');
            $table->unsignedBigInteger('remaining_seconds')->nullable()->after('xui_expiry_time');
            $table->timestamp('synced_at')->nullable()->after('xui_response');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'used_traffic_bytes',
                'remaining_traffic_bytes',
                'xui_expiry_time',
                'remaining_seconds',
                'synced_at',
            ]);
        });
    }
};
