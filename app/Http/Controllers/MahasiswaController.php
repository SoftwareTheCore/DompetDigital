<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\User;

class MahasiswaController extends Controller
{
    // Menampilkan dashboard mahasiswa
    public function index()
    {
        $user = Auth::user();
        $saldo = $user->saldo; 
        $transaksi = Transaction::where('user_id', $user->id)->latest()->get();

        return view('mahasiswa.dashboard', [
            'user' => $user,
            'saldo' => $saldo,
            'transaksi' => $transaksi,
        ]);
    }

    // Handle top up saldo
    public function topUp(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1000',
        ]);

        $user = Auth::user();
        $user->saldo += $request->jumlah;
        $user->save();

        Transaction::create([
            'user_id' => $user->id,
            'jenis' => 'topup',
            'jumlah' => $request->jumlah,
            'keterangan' => 'Top up saldo',
            'status' => 'sukses',
        ]);

        return redirect()->route('mahasiswa.dashboard')->with('success', 'Saldo berhasil ditambahkan!');
    }

    // Handle transfer saldo
    public function transfer(Request $request)
    {
        $request->validate([
            'penerima' => 'required|email|exists:users,email',
            'jumlah' => 'required|numeric|min:1000',
        ]);

        $user = Auth::user();
        if ($user->saldo < $request->jumlah) {
            return back()->withErrors(['jumlah' => 'Saldo Anda tidak mencukupi untuk transfer.']);
        }

        $recipient = User::where('email', $request->penerima)->first();
        $user->saldo -= $request->jumlah;
        $recipient->saldo += $request->jumlah;

        $user->save();
        $recipient->save();

        Transaction::create([
            'user_id' => $user->id,
            'jenis' => 'transfer',
            'jumlah' => $request->jumlah,
            'keterangan' => 'Transfer ke ' . $recipient->email,
            'status' => 'sukses',
        ]);

        Transaction::create([
            'user_id' => $recipient->id,
            'jenis' => 'receive',
            'jumlah' => $request->jumlah,
            'keterangan' => 'Penerimaan dari ' . $user->email,
            'status' => 'sukses',
        ]);

        return redirect()->route('mahasiswa.dashboard')->with('success', 'Transfer berhasil!');
    }

    // Handle withdraw saldo
    public function withdraw(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1000',
        ]);

        $user = Auth::user();
        if ($user->saldo < $request->jumlah) {
            return back()->withErrors(['jumlah' => 'Saldo Anda tidak mencukupi untuk melakukan penarikan.']);
        }

        $user->saldo -= $request->jumlah;
        $user->save();

        Transaction::create([
            'user_id' => $user->id,
            'jenis' => 'withdraw',
            'jumlah' => $request->jumlah,
            'keterangan' => 'Penarikan saldo',
            'status' => 'sukses',
        ]);

        return redirect()->route('mahasiswa.dashboard')->with('success', 'Penarikan berhasil!');
    }
}