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
            // Asumsi vendor_id merujuk ke tabel users
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('location');
            $table->string('connector_type');
            $table->decimal('capacity_kw', 8, 2);
            $table->string('photo_path'); // Menyimpan path file foto
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('inactive');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charger_machines');
    }
};