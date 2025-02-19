<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\User;
use App\Models\DailyReport;

class MahasiswaController extends Controller
{
    // Menampilkan dashboard mahasiswa
    public function index()
    {
        $user = Auth::user();

        // Hitung saldo berdasarkan transaksi yang sudah disetujui
        $approvedBalance = Transaction::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereIn('type', ['topup', 'receive'])
            ->sum('amount') - 
            Transaction::where('user_id', $user->id)
                ->whereIn('type', ['transfer', 'withdraw'])
                ->where('status', 'completed')
                ->sum('amount');

        $transaksi = Transaction::where('user_id', $user->id)->latest()->get();

        // Cek apakah ada transaksi top-up yang masih pending
        $pendingTopup = Transaction::where('user_id', $user->id)
            ->where('type', 'topup')
            ->where('status', 'pending')
            ->exists();

        return view('mahasiswa.dashboard', compact('user', 'approvedBalance', 'transaksi', 'pendingTopup'));
    }

    // Mengajukan top-up saldo
    public function topUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000'
        ]);

        $user = Auth::user();

        // Cek jika ada top-up yang masih pending
        if (Transaction::where('user_id', $user->id)->where('type', 'topup')->where('status', 'pending')->exists()) {
            return redirect()->back()->with('error', 'Anda masih memiliki permintaan Top Up yang belum diproses.');
        }

        DB::beginTransaction();
        try {
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'topup',
                'amount' => $request->amount,
                'status' => 'pending'
            ]);

            DB::commit();
            return redirect()->back()->with('info', 'Permintaan Top Up dikirim. Menunggu persetujuan dari staff.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Top Up Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Top Up gagal: Silakan coba lagi.');
        }
    }

    // Menyetujui transaksi top-up oleh admin
    public function approveTopUp($transactionId)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::lockForUpdate()->findOrFail($transactionId);

            if ($transaction->status !== 'pending') {
                return redirect()->back()->with('error', 'Transaksi sudah diproses sebelumnya.');
            }

            $user = User::findOrFail($transaction->user_id);
            $user->increment('saldo', $transaction->amount);

            // Update transaksi ke status approved
            $transaction->update(['status' => 'approved']);

            // Update laporan harian
            DailyReport::updateOrCreate(
                ['report_date' => now()->toDateString()],
                ['total_topup' => DB::raw("total_topup + {$transaction->amount}")]
            );

            DB::commit();
            return redirect()->back()->with('success', 'Top Up berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve TopUp Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyetujui Top Up.');
        }
    }

    // Handle transfer saldo
    public function transfer(Request $request)
    {
        $request->validate([
            'to_email' => 'required|exists:users,email',
            'amount' => 'required|numeric|min:10000'
        ]);

        $sender = Auth::user();
        $receiver = User::where('email', $request->to_email)->first();
        $amount = $request->amount;

        if ($sender->id === $receiver->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat mentransfer saldo ke akun sendiri.');
        }

        DB::beginTransaction();
        try {
            $sender = User::lockForUpdate()->find($sender->id);
            $receiver = User::lockForUpdate()->find($receiver->id);

            if ($sender->saldo < $amount) {
                return redirect()->back()->with('error', 'Saldo tidak mencukupi.');
            }

            $sender->decrement('saldo', $amount);
            $receiver->increment('saldo', $amount);

            Transaction::create([
                'user_id' => $sender->id,
                'type' => 'transfer',
                'amount' => $amount,
                'to_user_id' => $receiver->id,
                'status' => 'completed'
            ]);

            DailyReport::updateOrCreate(
                ['report_date' => now()->toDateString()],
                ['total_transfer' => DB::raw("total_transfer + {$amount}")]
            );

            DB::commit();
            return redirect()->back()->with('success', 'Transfer berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transfer Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Transfer gagal. Coba lagi.');
        }
    }
}
