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
            <a class="navbar-brand" href="#">Mahasiswa Dashboard</a>
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
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Top Up</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#topUpModal">Top Up</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Transfer</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#transferModal">Transfer</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Withdraw</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#withdrawModal">Withdraw</button>
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

    <!-- Modal Top Up -->
    <div class="modal fade" id="topUpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('mahasiswa.topup') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Top Up Saldo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah</label>
                            <input type="number" name="amount" class="form-control" min="1000" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Top Up</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Transfer -->
    <div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('mahasiswa.transfer') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Transfer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="recipient_email" class="form-label">Email Penerima</label>
                            <input type="email" name="recipient_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah</label>
                            <input type="number" name="amount" class="form-control" min="1000" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Transfer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Withdraw -->
    <div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('mahasiswa.withdraw') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Withdraw</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah</label>
                            <input type="number" name="amount" class="form-control" min="1000" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Withdraw</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
