<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('theme/images/favicon.png') }}">
    <link href="{{ asset('theme/css/style.css') }}" rel="stylesheet">
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container-fluid h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-4">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <h4 class="text-center mb-4">Sign up your account</h4>
                                    <form action="{{ route('admin.register.submit') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label><strong>Username</strong></label>
                                            <input type="text" name="name" class="form-control"
                                                placeholder="username">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Email</strong></label>
                                            <input type="email" name="email" class="form-control"
                                                placeholder="hello@example.com">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Password</strong></label>
                                            <input type="password" name="password" class="form-control"
                                                value="Password">
                                        </div>
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary btn-block">Sign me up</button>
                                        </div>
                                    </form>
                                    <div class="new-account mt-3">
                                        <p>Already have an account? <a class="text-primary"
                                                href="{{ url('admin/login') }}">Sign in</a></p>
                                    </div>
                                </div>
                            </div>
                            <div class="">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ asset('theme/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('theme/js/quixnav-init.js') }}"></script>
    <!--endRemoveIf(production)-->
</body>

</html>
