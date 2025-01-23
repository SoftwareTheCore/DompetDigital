<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Show admin dashboard
    public function index()
    {
        $totalUsers = DB::table('users')->count();
        $activeUsers = DB::table('users')
            ->where('updated_at', '>=', now()->subMinutes(30)) // contoh aktif dalam 30 menit terakhir
            ->count();
        $totalTransactions = DB::table('transactions')->count();
        $totalSaldo = DB::table('users')->sum('saldo');
        $users = DB::table('users')->get(); // Ambil semua data user untuk ditampilkan di modal

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'totalTransactions' => $totalTransactions,
            'totalSaldo' => $totalSaldo,
            'users' => $users,
        ]);
    }

    // Update user
    public function updateUser(Request $request, $id)
    {
        // Validasi input role
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,mahasiswa,bank mini', // Pastikan role valid sesuai dengan nilai yang diharapkan
        ]);

        // Perbarui data user
        DB::table('users')->where('id', $id)->update([
            'name' => $request->input('name'),
            'role' => $request->input('role'), // Simpan role yang valid
            'updated_at' => now(),
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('admin.dashboard')->with('success', 'Pengguna berhasil diperbarui.');
    }

    // Delete user
    public function deleteUser($id)
    {
        // Cari data pengguna berdasarkan ID
        $user = DB::table('users')->where('id', $id)->first();

        // Cek apakah pengguna ditemukan
        if (!$user) {
            return redirect()->route('admin.dashboard')->with('error', 'Pengguna tidak ditemukan.');
        }

        // Cek apakah pengguna yang dihapus adalah admin yang sedang login
        if (Auth::id() === $id) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Hapus data pengguna
        DB::table('users')->where('id', $id)->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('admin.dashboard')->with('success', 'Pengguna berhasil dihapus.');
    }
}
