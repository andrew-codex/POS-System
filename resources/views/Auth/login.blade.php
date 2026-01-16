<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
            integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
            crossorigin="anonymous"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>POS Login</title>
</head>

<body>
    <div class="split-container">
        <div class="left-panel">
            <div class="brand-content">
                <div class="logo-area">
                    <span class="shield-icon"></span>
                    <h1>POS System</h1>
                </div>
                <p class="description">Manage your retail operations with our powerful point-of-sale solution</p>
                
                <div class="image-card">
                    <img src="{{ asset('images/login-image.jpg') }}" alt="POS Illustration">
                </div>

                <div class="features">
                    <span><i class="dot"></i> Inventory</span>
                    <span><i class="dot"></i> Sales</span>
                    <span><i class="dot"></i> Reports</span>
                </div>
            </div>
        </div>

        <div class="right-panel">
            <div class="login-card">
                <div class="form-header">
                    <h2>Welcome back</h2>
                    <p>Sign in to your admin account</p>
                </div>

                <form action="{{ route('auth.login.submit') }}" method="POST">
                    @csrf
                  <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="admin@company.com" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                    <div class="form-footer">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span> Remember me
                        </label>
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-sign-in">Sign In</button>
                </form>
            </div>
            <footer class="copyright">
                © 2026 POS System. All rights reserved.
            </footer>
        </div>
    </div>
    <script>
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: '5000'
    };

    @if(session('success'))
    toastr.success(@json(session('success')));
    @endif

    @if(session('error'))
    toastr.error(@json(session('error')));
    @endif

    @if($errors->any())
    @foreach($errors->all() as $error)
    toastr.error(@json($error));
    @endforeach
    @endif
    </script>
</body>

</html>