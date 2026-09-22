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
        Schema::create('anime_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_title_id')->constrained('anime_titles')->restrictOnDelete();
            $table->foreignId('streaming_service_id')->constrained('streaming_services')->restrictOnDelete();
            $table->string('availability_status', 50);
            $table->timestamps();
            $table->unique(['anime_title_id', 'streaming_service_id', 'availability_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anime_availabilities');
    }
};
