<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('xui_base_url')->nullable();
            $table->string('subscription_base_url')->nullable();
            $table->string('xui_username')->nullable();
            $table->text('xui_password')->nullable();
            $table->unsignedBigInteger('xui_inbound_id')->nullable();
            $table->unsignedBigInteger('price_per_gb')->default(0);
            $table->integer('default_expiry_days')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
