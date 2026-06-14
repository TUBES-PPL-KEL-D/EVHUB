<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $spkluId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000',
        ], [
            'comment.required' => 'Kolom komentar ulasan wajib diisi.',
            'comment.min' => 'Komentar ulasan minimal terdiri dari 3 karakter.',
        ]);

        \App\Models\Review::create([
            'user_id' => auth()->id() ?? 1, 
            'spklu_id' => $spkluId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Ulasan berhasil ditambahkan!');
    }
}