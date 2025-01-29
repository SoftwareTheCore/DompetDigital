<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Wallet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .nav-item {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">Digital Wallet</a>
            <div class="d-flex">
                @if(auth()->check())
                    <span class="navbar-text me-3">
                        Welcome, {{ auth()->user()->name }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Article Content -->
    <div class="container article mt-4">
        <h1>Welcome to Digital Wallet</h1>
        <p>
            Digital Wallet is your one-stop solution for managing your finances securely and efficiently. 
            Whether you're an admin or a student, our platform provides you with the tools you need to 
            track transactions, manage accounts, and stay on top of your financial goals.
        </p>
        <p>
            Our mission is to make financial management simple, accessible, and secure for everyone. 
            With Digital Wallet, you can easily monitor your spending, set budgets, and achieve your 
            financial objectives with ease.
        </p>
        <p>
            Ready to get started? Click the <strong>Login</strong> button at the top right corner to 
            access your account and take control of your finances today!
        </p>

        <!-- Tombol Kembali -->
        <div class="mt-4">
            <button class="btn btn-secondary" onclick="goBack()">Kembali</button>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript untuk Tombol Kembali -->
    <script>
        function goBack() {
            if (document.referrer) {
                window.history.back();
            } else {
                window.location.href = "{{ url('/') }}";
            }
        }
    </script>

</body>
</html>
