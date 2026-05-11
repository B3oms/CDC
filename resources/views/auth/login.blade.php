<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPUP-CDC | Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="top-bar"></div>
<div class="yellow-bar"></div>

<div class="page-body">

    @if(file_exists(public_path('images/spup-logo.png')))
        <img src="{{ asset('images/spup-logo.png') }}" alt="SPUP Logo" class="logo">
    @else
        <div class="logo-placeholder">SPUP</div>
    @endif

    <h1>Login</h1>

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <input
            type="email"
            name="email"
            placeholder="email"
            value="{{ old('email') }}"
            required
            autofocus
        >

        @if($errors->has('email'))
            <span class="error-msg">{{ $errors->first('email') }}</span>
        @endif

        <input
            type="password"
            name="password"
            placeholder="password"
            required
        >

        <button type="submit" class="btn-login">Login</button>
    </form>

</div>

</body>
</html>