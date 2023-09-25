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
        Schema::create('customer_feedback', function (Blueprint $table) {
            $table->id();
            $table->string("feedback_name")->nullable();
            $table->string("feedback_email")->nullable();
            $table->string("feedback_phone")->nullable();
            $table->string("feedback_subject")->nullable();
            $table->string("feedback_file")->nullable();
            $table->text('feedback_message')->nullable();
            $table->string('ticket_id')->unique()->nullable();
            $table->string('user_id')->nullable();
            $table->dateTime('submitted_on')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_feedback');
    }
};
