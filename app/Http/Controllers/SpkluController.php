<?php

namespace App\Http\Controllers;

use App\Models\Spklu; 
use Illuminate\Http\Request;

class SpkluController extends Controller
{
    public function index()
    {
        // Mengambil data koordinat dan info SPKLU dari database
        // Kolom yang diambil disesuaikan dengan kebutuhan peta
        $spklus = Spklu::select('name', 'address', 'latitude', 'longitude')->get();

        // Mengirimkan data $spklus ke file resources/views/welcome.blade.php
        return view('vendor.map', compact('spklus'));

    }

    public function getSpkluData()
    {
        // Mengambil data SPKLU beserta relasi daftar mesin charger
        $spklus = Spklu::with('chargers')->get();
        return response()->json($spklus);
    }
}