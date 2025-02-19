<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();
        try {
            $transaction = Transaction::lockForUpdate()->findOrFail($id);

            if ($transaction->status !== 'pending') {
                DB::rollBack();
                return redirect()->back()->withErrors(['error' => 'Transaksi sudah diproses sebelumnya.']);
            }

            $transaction->status = 'approved';
            $transaction->save();

            if ($transaction->type === 'topup') {
                $user = User::lockForUpdate()->findOrFail($transaction->user_id);
                $user->saldo += $transaction->amount;
                $user->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Transaksi disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menyetujui transaksi: ' . $e->getMessage()]);
        }
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
