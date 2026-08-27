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
        Schema::create('service_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')->references('id')->on('vehicles');
            $table->int('service_type');       // controlado via PHP Enum no Model
            $table->text('description')->nullable();
            $table->date('service_date');
            $table->decimal('cost', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_records');
    }
};
