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
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <!-- Saldo Section -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-success h-100">
                    <div class="card-body">
                        <h5 class="card-title">Saldo Anda</h5>
                        <p class="card-text h3">
                            Rp {{ number_format(Auth::user()->saldo, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            @if($pendingTopup)
            <div class="col-md-4">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body">
                        <h5 class="card-title">Saldo Pending</h5>
                        <p class="card-text h3">
                            Rp {{ number_format(optional($pendingTopup)->amount, 2, ',', '.') }}
                        </p>
                        <small>Menunggu persetujuan staff</small>
                    </div>
                </div>
            </div>
            @endif
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
                            <button type="submit" class="btn btn-primary w-100" {{ $pendingTopup ? 'disabled' : '' }}>
                                Top Up
                            </button>
                        </form>
                        @if($pendingTopup)
                            <div class="alert alert-warning mt-2">
                                <small>Anda memiliki top up pending sebesar Rp {{ number_format(optional($pendingTopup)->amount, 2, ',', '.') }}</small>
                            </div>
                        @endif
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
