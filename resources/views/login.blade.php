<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <main class="col-md-2 col-lg-3">
        @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
        <div class="card shadow">
            <div class="card-header bg-primary text-center text-white">
                <h3 class="m-1">Login</h3>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('login.custom') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        @if ($errors->has('emailPassword'))
                        <span class="text-danger">{{ $errors->first('emailPassword') }}</span>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mb-3">Login</button>
                </form>
                <div class="text-center">
                    <a href="{{ route('register.user') }}" class="btn btn-link">New User ? Register here..</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
