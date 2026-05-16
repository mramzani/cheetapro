<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('settings', 'subscription_base_url')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('subscription_base_url')->nullable()->after('xui_base_url');
            });
        }

        if (Schema::hasColumn('settings', 'subscription_base_url')) {
            DB::table('settings')
                ->whereNull('subscription_base_url')
                ->update(['subscription_base_url' => 'https://sub.cheeta.site:2096/sub']);
        }

        if (! Schema::hasColumn('clients', 'sub_id')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('sub_id')->nullable()->unique()->after('email');
            });
        }

        if (! Schema::hasColumn('clients', 'config_link')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->text('config_link')->nullable()->after('status');
            });
        }

        if (! Schema::hasColumn('clients', 'subscription_link')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->text('subscription_link')->nullable()->after('config_link');
            });
        }
    }

    public function down(): void
    {
        // These columns are now part of the table creation migrations.
    }
};
