<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function show($id)
    {
        return User::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update($request->all());

        return response()->json([
            'message' => 'User berhasil diupdate',
            'user' => $user
        ]);
    }

    public function destroy($id)
    {
        User::destroy($id);

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
}