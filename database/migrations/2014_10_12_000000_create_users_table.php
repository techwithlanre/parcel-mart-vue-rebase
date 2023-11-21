<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->foreignId('user_type_id')->nullable();
            $table->string('business_name')->nullable();
            $table->foreignId('business_industry_id')->nullable();
            $table->foreignId('country_id')->nullable();
            $table->foreignId('state_id')->nullable();
            $table->string('firebase_token', 500)->nullable();
            $table->string('address', 500)->nullable();
            $table->integer('point')->nullable();// 65
            $table->integer('ref_code')->nullable();
            $table->decimal('credit_limit')->nullable();
            $table->foreignId('ref_by_id')->nullable();
            $table->foreignId('currency_id');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
