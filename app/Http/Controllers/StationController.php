<?php

namespace App\Http\Controllers;

use App\Models\Spklu;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function getDetail($id)
    {
        // Mencari SPKLU berdasarkan ID dan memuat data mesin charger yang berelasi
        // Catatan: Pastikan di file model Spklu.php kamu sudah membuat fungsi relasi bernama 'chargers'
        $spklu = Spklu::with('chargers')->find($id);

        if (!$spklu) {
            return response()->json(['message' => 'Stasiun tidak ditemukan'], 404);
        }

        return response()->json($spklu);
    }
}
