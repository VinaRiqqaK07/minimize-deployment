<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserAdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')->latest()->get();

        return view('admin.users.index', compact('admins'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $admin = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        AuditLog::record('admin.created', $admin, ['name' => $admin->name, 'email' => $admin->email]);

        return back()->with('status', 'Admin baru "'.$admin->name.'" berhasil ditambahkan.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($user->role === 'admin', 404);
        abort_if($user->id === $request->user()->id, 422, 'Kamu tidak bisa menghapus akunmu sendiri dari sini.');

        AuditLog::record('admin.removed', null, ['name' => $user->name, 'email' => $user->email]);

        // Bukan dihapus total (biar riwayat approve/reject dia tetap tercatat),
        // cukup turunkan role-nya jadi buyer biasa supaya kehilangan akses admin.
        $user->update(['role' => 'buyer']);

        return back()->with('status', 'Akses admin untuk "'.$user->name.'" dicabut.');
    }
}
