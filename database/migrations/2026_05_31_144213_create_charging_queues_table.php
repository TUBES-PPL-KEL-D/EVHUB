<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charging_queues', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('charger_machine_id')
                ->constrained('charger_machines')
                ->onDelete('cascade');

            $table->enum('status', ['waiting', 'cancelled', 'done'])
                ->default('waiting');

            $table->timestamp('queued_at')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charging_queues');
    }
};