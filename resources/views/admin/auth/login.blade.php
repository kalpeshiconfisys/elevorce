<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Elevorce</title>
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo/temp_logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            max-width: 900px;
            width: 100%;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            display: flex;
        }

        .login-left {
            flex: 1;
            background: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-left img {
            max-width: 100%;
            height: auto;
        }

        .login-right {
            flex: 1;
            background: #fff;
            padding: 3rem;
        }

        .login-title {
            font-weight: 700;
            margin-bottom: 2rem;
            color: #222;
        }

        .form-control {
            border-radius: 6px;
            padding: 12px;
        }

        .btn-login {
            background-color: #3b82f6;
            border: none;
            border-radius: 6px;
            padding: 10px;
            color: #fff;
            font-weight: 600;
            width: 100%;
            transition: 0.3s;
        }

        .btn-login:hover {
            background-color: #2563eb;
        }

        .social-login {
            margin-top: 1rem;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .social-login a img {
            height: 35px;
            width: 35px;
        }

        .footer-link {
            margin-top: 1rem;
            text-align: center;
            font-size: 14px;
        }

        .footer-link a {
            text-decoration: none;
            font-weight: 600;
            color: #3b82f6;
        }
    </style>
</head>

<body>
    <div class="login-container">

        <div class="login-right">
            <h2 class="login-title">Login</h2>
                @if (session('success'))
                    <div class="alert alert-success custom-alert">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger custom-alert">{{ session('error') }}</div>
                @endif
            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email address" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                <button type="submit" class="w-100 btn btn-login" style="background-color: #D79838 ;color: #fff">Log in</button>
            </form>
            <div class="social-login">

            </div>
            <div class="footer-link">
                {{-- Don’t have an account? <a href="#">Create an account</a> --}}
            </div>
        </div>
        <div class="login-left">
            <img src="{{ asset('images/logo/temp_logo.png') }}" alt="Illustration">
        </div>

        <!-- Right side form -->

    </div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
     setTimeout(function() {
                $('.custom-alert').slideUp(500);
            }, 3000);
</script>

</html>
