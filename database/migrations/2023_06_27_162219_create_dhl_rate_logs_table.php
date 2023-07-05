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
        Schema::create('dhl_rate_logs', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_code');
            $table->string('local_product_code');
            $table->string('network_type_code');
            $table->text('weight');
            $table->text('total_price');
            $table->text('total_price_breakdown');
            $table->text('detailed_price_breakdown');
            $table->text('pickup_capabilities');
            $table->text('delivery_capabilities');
            $table->date('pricing_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dhl_rate_logs');
    }
};
