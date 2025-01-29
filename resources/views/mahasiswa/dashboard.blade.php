<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Mahasiswa Dashboard</a>        
                <div class="ms-auto d-flex align-items-center">
                <span class="text-white me-3">Selamat datang, <strong>{{ Auth::user()->name }}</strong></span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Pesan Sukses/Error -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Saldo -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-success h-100">
                    <div class="card-body">
                        <h5 class="card-title">Saldo Anda</h5>
                        <p class="card-text h3">Rp {{ number_format($user->saldo, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fitur Transaksi -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Top Up</h5>
                        <form action="{{ route('mahasiswa.topup') }}" method="POST">
                            @csrf
                            <input type="number" name="amount" placeholder="Jumlah Top Up" min="10000" class="form-control mb-2" required>
                            <button type="submit" class="btn btn-primary w-100">Top Up</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Transfer</h5>
                        <form action="{{ route('mahasiswa.transfer') }}" method="POST">
                            @csrf
                            <input type="email" name="to_email" placeholder="Email Penerima" class="form-control mb-2" required>
                            <input type="number" name="amount" placeholder="Jumlah Transfer" min="10000" class="form-control mb-2" required>
                            <button type="submit" class="btn btn-primary w-100">Transfer</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Withdraw</h5>
                        <form action="{{ route('mahasiswa.withdraw') }}" method="POST">
                            @csrf
                            <input type="number" name="amount" placeholder="Jumlah Withdraw" min="10000" class="form-control mb-2" required>
                            <button type="submit" class="btn btn-primary w-100">Withdraw</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Transaksi -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Riwayat Transaksi</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Jenis Transaksi</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaksi as $trans)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $trans->created_at }}</td>
                                            <td>{{ ucfirst($trans->type) }}</td>
                                            <td>Rp {{ number_format($trans->amount, 2, ',', '.') }}</td>
                                            <td>
                                                @if ($trans->type == 'transfer')
                                                    Transfer ke {{ $trans->recipient->name ?? 'Tidak Diketahui' }}
                                                @elseif ($trans->type == 'receive')
                                                    Diterima dari {{ $trans->user->name ?? 'Tidak Diketahui' }}
                                                @else
                                                    {{ ucfirst($trans->type) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
