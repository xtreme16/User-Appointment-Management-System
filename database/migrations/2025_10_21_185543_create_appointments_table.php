<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->uuid('creator_id');
            $table->datetime('start');
            $table->datetime('end');
            $table->timestamps();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('appointment_user', function (Blueprint $table) {
            $table->uuid('appointment_id');
            $table->uuid('user_id');

            $table->primary(['appointment_id', 'user_id']);
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_user');
        Schema::dropIfExists('appointments');
    }
};
