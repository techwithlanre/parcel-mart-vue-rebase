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
        Schema::create('tracking_logs', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_id');
            $table->string('waybill_number')->nullable();
            $table->string('update_code')->nullable();
            $table->string('update_description')->nullable();
            $table->string('update_location')->nullable();
            $table->string('comment')->nullable();
            $table->string('problem_code')->nullable();
            $table->string('gross_weight')->nullable();
            $table->string('chargeable_weight')->nullable();
            $table->string('weight_unit')->nullable();
            $table->string('provider');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_logs');
    }
};
