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
        Schema::create('dhl_shipment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->references('id')->on('shipments');
            $table->string('shipment_tracking_number');
            $table->string('tracking_url');
            $table->text('document_content');
            $table->text('package_details');
            $table->string('type_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dhl_shipment_logs');
    }
};
