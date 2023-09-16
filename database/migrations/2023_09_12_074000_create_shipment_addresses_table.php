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
        Schema::create('shipment_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('business_name')->nullable();
            $table->string('address_1', 500);
            $table->string('address_2', 500);
            $table->string('landmark', 500);
            $table->integer('country_id');
            $table->integer('state_id');
            $table->string('city_id');
            $table->string('postcode');
            $table->enum('type', ['origin', 'destination']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_addresses');
    }
};
