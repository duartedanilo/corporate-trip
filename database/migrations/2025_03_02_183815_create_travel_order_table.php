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
        Schema::create('travel_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester');
            $table->string('destination');
            $table->dateTime('departure_date');
            $table->dateTime('return_date');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->foreign('requester')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_order');
    }
};
