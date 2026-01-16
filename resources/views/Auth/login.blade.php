<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
            integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
            crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"
            integrity="sha512-L3K7QqzX3r2X8q8N1k3p0QmO4b5v6gk2y7h9Y5h5VfZPZrj0p1l6e0b9s5c3d2f1g0h9Y5h5VfZPZrj0p1l6e0w=="
            crossorigin="anonymous"></script>
        <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css"
          integrity="sha512-Gk6czS4fFqqpwYBFvToNiOfzmiEJeZVrDCTnhgypKekJy5o+1OtSWT8gJtIhXCTITVEWilQoNqT9XyM54q9Wug=="
          crossorigin="anonymous">
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
                <label class="required" for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="youremail@example.com" value="{{ old('email') }}"
                    required>
            </div>
            <div class="form-group">
                <label class="required" for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="******" required>
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

    @if($errors->any())
    @foreach($errors->all() as $error)
    toastr.error(@json($error));
    @endforeach
    @endif
    </script>
</body>

</html>