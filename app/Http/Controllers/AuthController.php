<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function checkUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255'
        ]);

        $email = Str::lower(trim($request->query('email')));
        
        return response()->json([
            'exists' => User::whereRaw('LOWER(email) = ?', [$email])->exists(),
            'email' => $email
        ]);
    }
}