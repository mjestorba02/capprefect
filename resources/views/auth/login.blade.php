<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prefect Department - Student Management System</title>
    <link rel="stylesheet" href="{{ asset('styles/main.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/tables.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/modals.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Login Screen -->
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-user-shield login-icon"></i>
                <h1>Prefect Department</h1>
                <p>Student Management System</p>
            </div>

            <form method="POST" action="{{ route('login.submit') }}" class="login-form">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Sign In</button>

                @if ($errors->any())
                    <p style="color:red; margin-top:10px;">{{ $errors->first('login') }}</p>
                @endif
            </form>

            <div class="demo-accounts">
                <p><strong>Demo Accounts:</strong></p>
                <p><strong>Admin:</strong> admin / admin123</p>
                <p><strong>Prefect Officer:</strong> prefect / prefect123</p>
                <p><strong>Guidance Staff:</strong> guidance / guide123</p>
            </div>
        </div>
    </div>
</body>
</html>