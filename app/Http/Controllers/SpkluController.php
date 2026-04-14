<?php

namespace App\Http\Controllers;

use App\Models\Spklu; // Pastikan model Spklu di-import
use Illuminate\Http\Request;

class SpkluController extends Controller
{
    public function index()
    {
        // Mengambil data koordinat dan info SPKLU dari database
        // Kolom yang diambil disesuaikan dengan kebutuhan peta
        $spklus = Spklu::select('name', 'address', 'latitude', 'longitude')->get();

        // Mengirimkan data $spklus ke file resources/views/welcome.blade.php
        return view('welcome', compact('spklus'));
    }
}