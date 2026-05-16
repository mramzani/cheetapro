<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('subscription_base_url')->nullable()->after('xui_base_url');
        });

        DB::table('settings')
            ->whereNull('subscription_base_url')
            ->update(['subscription_base_url' => 'https://sub.cheeta.site:2096/sub']);

        Schema::table('clients', function (Blueprint $table) {
            $table->string('sub_id')->nullable()->unique()->after('email');
            $table->text('config_link')->nullable()->after('status');
            $table->text('subscription_link')->nullable()->after('config_link');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['sub_id', 'config_link', 'subscription_link']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('subscription_base_url');
        });
    }
};
