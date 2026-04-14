<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charger_machines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('spklu_id')->constrained('spklus')->onDelete('cascade');
            $table->string('name');
            $table->string('connector_type');
            $table->decimal('capacity_kw', 8, 2);
            $table->decimal('price_per_kwh', 10, 2);
            $table->string('operational_hours');
            $table->string('photo_path'); 
            $table->enum('status', ['available', 'unavailable', 'maintenance'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charger_machines');
    }
};