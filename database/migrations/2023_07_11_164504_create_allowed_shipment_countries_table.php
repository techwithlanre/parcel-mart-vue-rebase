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
        /*Schema::create('allowed_shipment_countries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->references('id')->on('countries');
            $table->text('allowed_destinations')->comment('list of country ids');
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowed_shipment_countries');
    }
};
