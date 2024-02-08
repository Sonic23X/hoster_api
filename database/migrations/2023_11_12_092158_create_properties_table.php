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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedInteger('owner_id');
            $table->string('title', 80);
            $table->string('address', 80);
            $table->integer('rooms');
            $table->integer('beds');
            $table->integer('bathrooms');
            $table->text('about');
            $table->text('additional_information');
            $table->text('security');
            $table->text('arrive');
            $table->boolean('rules')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
