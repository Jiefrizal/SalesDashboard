<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /** Show the login form. */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    /** Log in directly as Viewer (read-only) without entering any credentials. */
    public function loginViewer(Request $request)
    {
        $viewer = User::where('role', 'viewer')->first();

        if (!$viewer) {
            $viewer = User::firstOrCreate(
                ['email' => 'viewer@aspacindo.com'],
                [
                    'name'          => 'Viewer',
                    'password'      => '',
                    'role'          => 'viewer',
                    'allowed_menus' => ['dashboard', 'stu_unit', 'stok_unit'],
                ]
            );
        } else {
            // Ensure role is strictly viewer
            if ($viewer->role !== 'viewer') {
                $viewer->update(['role' => 'viewer']);
            }
        }

        Auth::login($viewer, true);
        $request->session()->regenerate();

        return redirect('/');
    }

    /** Handle login form submission. */
    public function login(Request $request)
    {
        $username = trim($request->input('username', $request->input('email', '')));

        // If username is empty or viewer, log in directly as Viewer
        if (empty($username) || $username === 'viewer@aspacindo.com' || $username === 'viewer') {
            return $this->loginViewer($request);
        }

        // Super Admin or specific user login
        $credentials = [
            'email'    => $username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()
            ->withInput($request->only('username', 'email'))
            ->withErrors(['username' => 'User Name atau Password salah.']);
    }

    /** Handle logout. */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
