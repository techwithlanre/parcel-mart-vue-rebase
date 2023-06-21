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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('origin_address_id');
            $table->unsignedBigInteger('destination_address_id');
            $table->string('status');
            $table->decimal('shipment_price')->nullable();
            $table->decimal('shipment_paid_amount')->nullable();
            $table->decimal('insurance_amount')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('insurance_id')->nullable();
            $table->string('provider')->nullable('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
