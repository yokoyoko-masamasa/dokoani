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
        Schema::create('streaming_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo_image_url', 512)->nullable();
            $table->integer('tmdb_provider_id')->unique();
            $table->string('service_url', 512);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streaming_services');
    }
};
