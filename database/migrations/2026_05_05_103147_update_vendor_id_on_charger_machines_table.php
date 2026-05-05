<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('charger_machines', function (Blueprint $table) {
            // Hapus foreign key lama
            $table->dropForeign(['vendor_id']);
            
            // Buat foreign key baru yang strict ke tabel vendors
            $table->foreign('vendor_id')
                  ->references('id')->on('vendors')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('charger_machines', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->foreign('vendor_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }
};