<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi (menambahkan kolom baru).
     */
    public function up(): void
    {
        Schema::table('vendor_withdrawals', function (Blueprint $table) {
            // Menambahkan kolom receipt_path setelah kolom admin_notes
            $table->string('receipt_path')->nullable()->after('admin_notes');
        });
    }

    /**
     * Kembalikan migrasi (menghapus kolom jika di-rollback).
     */
    public function down(): void
    {
        Schema::table('vendor_withdrawals', function (Blueprint $table) {
            $table->dropColumn('receipt_path');
        });
    }
};