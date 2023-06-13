<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referral_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('referred_by_id');
            $table->boolean('is_paid')->default('0');
            $table->dateTime('date_paid')->nullable();
            $table->foreignId('referral_payment_log_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_logs');
    }
};
