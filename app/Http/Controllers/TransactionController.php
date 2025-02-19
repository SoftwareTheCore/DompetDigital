<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
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
                return redirect()->back()->with('error', 'Transaksi sudah diproses sebelumnya.');
            }

            if ($transaction->type === 'topup') {
                $user = User::lockForUpdate()->findOrFail($transaction->user_id);
                $user->saldo += $transaction->amount;
                $user->save();
            }

            $transaction->status = 'approved';
            $transaction->save();

            DB::commit();
            return redirect()->back()->with('success', 'Transaksi disetujui dan saldo diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyetujui transaksi: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        $transaction = Transaction::findOrFail($id);
        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi sudah diproses sebelumnya.');
        }

        $transaction->status = 'rejected';
        $transaction->save();
        
        return redirect()->back()->with('error', 'Transaksi ditolak.');
    }
}
