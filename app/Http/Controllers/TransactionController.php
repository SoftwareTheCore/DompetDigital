<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\BankMini;
use App\Models\User;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('status', 'pending')->get();
        return view('staff.dashboard', compact('transactions'));
    }

    public function approve($id)
    {
        $transaction = Transaction::findOrFail($id);
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
        $transaction = Transaction::findOrFail($id);
        $transaction->status = 'rejected';
        $transaction->save();
        return redirect()->back()->with('error', 'Transaksi ditolak.');
    }
}
