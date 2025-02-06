<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StaffBankMiniController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'staff']);
    }

    public function dashboard()
    {
        $transactions = Transaction::where('status', 'pending')->get();
        return view('staff.dashboard', compact('transactions'));
    }

    public function approve($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->withErrors(['error' => 'Transaksi tidak ditemukan.']);
        }

        $transaction->status = 'approved';
        $transaction->save();

        if ($transaction->type === 'top_up') {
            $user = User::findOrFail($transaction->user_id);
            $user->saldo += $transaction->amount;
            $user->save();
        }

        return redirect()->back()->with('success', 'Transaksi disetujui.');
    }

    public function reject($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->withErrors(['error' => 'Transaksi tidak ditemukan.']);
        }

        $transaction->status = 'rejected';
        $transaction->save();
        return redirect()->back()->with('error', 'Transaksi ditolak.');
    }
}
