@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Dashboard Staff Bank Mini</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Jenis Transaksi</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->user->name }}</td>
                    <td>{{ ucfirst($transaction->type) }}</td>
                    <td>Rp{{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ ucfirst($transaction->status) }}</td>
                    <td>
                        <form action="{{ route('staff.approve', $transaction->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-success">Setujui</button>
                        </form>
                        <form action="{{ route('staff.reject', $transaction->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-danger">Tolak</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
