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
        Schema::create('address_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('address_id');
            $table->string('contact_name');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone');
            $table->boolean('is_default')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_contacts');
    }
};
