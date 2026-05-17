<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('email')->unique();
            $table->string('sub_id')->unique();
            $table->unsignedBigInteger('inbound_id');
            $table->unsignedInteger('total_gb');
            $table->unsignedBigInteger('total_bytes');
            $table->unsignedBigInteger('used_traffic_bytes')->nullable();
            $table->unsignedBigInteger('remaining_traffic_bytes')->nullable();
            $table->bigInteger('expiry_time')->default(0);
            $table->bigInteger('xui_expiry_time')->nullable();
            $table->unsignedBigInteger('remaining_seconds')->nullable();
            $table->string('tg_id')->nullable();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('cost');
            $table->string('status')->default('active');
            $table->text('config_link')->nullable();
            $table->text('subscription_link')->nullable();
            $table->json('xui_response')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['seller_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
