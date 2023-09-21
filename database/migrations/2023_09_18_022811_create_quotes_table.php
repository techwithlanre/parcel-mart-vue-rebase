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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_country_id');
            $table->foreignId('origin_state_id');
            $table->foreignId('origin_city_id');
            $table->foreignId('destination_country_id');
            $table->foreignId('destination_state_id');
            $table->foreignId('destination_city_id');
            $table->integer('quantity');
            $table->integer('weight');
            $table->integer('length');
            $table->integer('width');
            $table->integer('height');
            $table->string('commercial_invoice');
            $table->string('parking_list');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
