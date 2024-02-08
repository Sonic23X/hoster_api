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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('property_id');
            $table->timestamp('check_in');
            $table->timestamp('check_out');
            $table->string('confirmation_code', 6);
            $table->string('status');
            $table->timestamp('cancel_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
