<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>POS Login</title>

</head>

<body>

    <div class="login-card">
        <div class="brand">
            <h1>POS System</h1>
            <p>Sign in to continue</p>
        </div>

        <form action="{{ route('auth.login.submit') }}" method="POST">
            @csrf
            <div class="form-group ">
                <label class="required">Email</label>
                <input type="email" name="email" placeholder="youremail@example.com" value="{{ old('email') }}"
                    required>
            </div>
            <div class="form-group">
                <label class="required">Password</label>
                <input type="password" name="password" placeholder="******" required>
            </div>
            <button type="submit">Login</button>
        </form>
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

    @if($errors-> any())
    @foreach($errors-> all() as $error)
    toastr.error(@json($error));
    @endforeach
    @endif
    </script>
</body>

</html>