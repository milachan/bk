<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderBy('name')->paginate(20);
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6|confirmed',
            'role_id'   => 'required|exists:roles,id',
            'jabatan'   => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,'.$user->id,
            'password'  => 'nullable|string|min:6|confirmed',
            'role_id'   => 'required|exists:roles,id',
            'jabatan'   => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
