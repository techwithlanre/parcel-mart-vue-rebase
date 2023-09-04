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
        Schema::create('ups_rate_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service_code');
            $table->string('packaging_type_code');
            $table->text('billing_weight');
            $table->text('total_price');
            $table->text('total_price_breakdown');
            $table->date('pricing_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ups_rate_logs');
    }
};
