<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:owner,cashier,inventory',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        // Use Auth facade explicitly for clarity
        Auth::login($user);

        // Redirect based on user role
        if ($user->isOwner()) {
            return redirect()->route('owner.dashboard');
        } elseif ($user->isCashier()) {
            return redirect()->route('cashier.dashboard');
        } elseif ($user->isInventory()) {
            return redirect()->route('inventory.dashboard');
        }

        // Default redirect
        return redirect()->route('dashboard');
    }
}
