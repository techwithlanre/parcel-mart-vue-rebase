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
        Schema::create('ups_shipment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->references('id')->on('shipments');
            $table->string('tracking_number');
            $table->string('identification_number');
            $table->text('document_content');
            $table->string('reference');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ups_shipment_logs');
    }
};
