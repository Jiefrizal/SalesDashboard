<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $query = User::orderBy('name', 'asc');

        // Super Admin accounts can only be viewed by Super Admin users
        if (!auth()->user()->isSuperAdmin()) {
            $query->whereNotIn('role', ['super_admin', 'admin']);
        }

        $users = $query->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|string|max:255|unique:users,email',
            'password'        => 'required|string|min:6|confirmed',
            'role'            => 'required|string|in:super_admin,editor,viewer',
            'allowed_menus'   => 'required|array|min:1',
            'allowed_menus.*' => 'string|in:dashboard,stu_unit,stok_unit,digital_marketing,cabang,users',
        ], [
            'name.required'          => 'Nama pengguna wajib diisi.',
            'email.required'         => 'Username / Email wajib diisi.',
            'email.unique'           => 'Username / Email telah terpakai oleh pengguna lain.',
            'password.required'      => 'Password wajib diisi.',
            'password.min'           => 'Password minimal 6 karakter.',
            'password.confirmed'     => 'Konfirmasi password tidak cocok.',
            'role.required'          => 'Jenis pengguna (Role) wajib dipilih.',
            'allowed_menus.required' => 'Pilih minimal 1 hak akses menu.',
            'allowed_menus.min'      => 'Pilih minimal 1 hak akses menu.',
        ]);

        if ($validated['role'] === 'super_admin' && !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki hak akses untuk membuat akun Super Admin.');
        }

        User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'password'      => Hash::make($validated['password']),
            'role'          => $validated['role'],
            'allowed_menus' => $validated['allowed_menus'],
        ]);

        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'Akun Super Admin tidak dapat dilihat atau dikelola selain oleh Super Admin.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'Akun Super Admin tidak dapat dilihat atau dikelola selain oleh Super Admin.');
        }

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'        => 'nullable|string|min:6|confirmed',
            'role'            => 'required|string|in:super_admin,editor,viewer',
            'allowed_menus'   => 'required|array|min:1',
            'allowed_menus.*' => 'string|in:dashboard,stu_unit,stok_unit,digital_marketing,cabang,users',
        ], [
            'name.required'          => 'Nama pengguna wajib diisi.',
            'email.required'         => 'Username / Email wajib diisi.',
            'email.unique'           => 'Username / Email telah terpakai oleh pengguna lain.',
            'password.min'           => 'Password minimal 6 karakter.',
            'password.confirmed'     => 'Konfirmasi password tidak cocok.',
            'role.required'          => 'Jenis pengguna (Role) wajib dipilih.',
            'allowed_menus.required' => 'Pilih minimal 1 hak akses menu.',
            'allowed_menus.min'      => 'Pilih minimal 1 hak akses menu.',
        ]);

        if ($validated['role'] === 'super_admin' && !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki hak akses untuk menetapkan peran Super Admin.');
        }

        $data = [
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'role'          => $validated['role'],
            'allowed_menus' => $validated['allowed_menus'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data akun pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'Akun Super Admin tidak dapat dihapus selain oleh Super Admin.');
        }

        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
