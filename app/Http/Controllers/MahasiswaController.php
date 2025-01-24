<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\User;
use App\Models\DailyReport;
use Illuminate\Support\Facades\DB;


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
            'amount' => 'required|numeric|min:10000'
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $amount = $request->amount;

            // Tambah saldo user
            $user->saldo += $amount;
            $user->save();

            // Catat transaksi
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'topup',
                'amount' => $amount
            ]);

            // Update daily report
            $today = now()->toDateString();
            $dailyReport = DailyReport::firstOrCreate(
                ['report_date' => $today],
                ['total_topup' => 0, 'total_withdrawal' => 0, 'total_transfer' => 0]
            );
            $dailyReport->total_topup += $amount;
            $dailyReport->save();

            DB::commit();
            return redirect()->back()->with('success', 'Top Up berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Top Up gagal: ' . $e->getMessage());
        }
    }

    // Handle transfer saldo
    public function transfer(Request $request)
    {
        $request->validate([
            'to_email' => 'required|exists:users,email',
            'amount' => 'required|numeric|min:10000'
        ]);

        DB::beginTransaction();
        try {
            $sender = Auth::user();
            $receiver = User::where('email', $request->to_email)->first();
            $amount = $request->amount;

            // Cek saldo cukup
            if ($sender->saldo < $amount) {
                return redirect()->back()->with('error', 'Saldo tidak mencukupi');
            }

            // Kurangi saldo pengirim
            $sender->saldo -= $amount;
            $sender->save();

            // Tambah saldo penerima
            $receiver->saldo += $amount;
            $receiver->save();

            // Catat transaksi
            Transaction::create([
                'user_id' => $sender->id,
                'type' => 'transfer',
                'amount' => $amount,
                'to_user_id' => $receiver->id
            ]);

            // Update daily report
            $today = now()->toDateString();
            $dailyReport = DailyReport::firstOrCreate(
                ['report_date' => $today],
                ['total_topup' => 0, 'total_withdrawal' => 0, 'total_transfer' => 0]
            );
            $dailyReport->total_transfer += $amount;
            $dailyReport->save();

            DB::commit();
            return redirect()->back()->with('success', 'Transfer berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Transfer gagal: ' . $e->getMessage());
        }
    }

    // Handle withdraw saldo
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000'
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $amount = $request->amount;

            // Cek saldo cukup
            if ($user->saldo < $amount) {
                return redirect()->back()->with('error', 'Saldo tidak mencukupi');
            }

            // Kurangi saldo user
            $user->saldo -= $amount;
            $user->save();

            // Catat transaksi
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'withdrawal',
                'amount' => $amount
            ]);

            // Update daily report
            $today = now()->toDateString();
            $dailyReport = DailyReport::firstOrCreate(
                ['report_date' => $today],
                ['total_topup' => 0, 'total_withdrawal' => 0, 'total_transfer' => 0]
            );
            $dailyReport->total_withdrawal += $amount;
            $dailyReport->save();

            DB::commit();
            return redirect()->back()->with('success', 'Withdraw berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Withdraw gagal: ' . $e->getMessage());
        }
    }
}
