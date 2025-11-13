@extends('layouts.navTop')

@section('content')
<style>
    body {
        background: #f0f2f5;
        font-family: 'Segoe UI', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }

    .login-box {
        width: 90vw;
        max-width: 500px;
        background: #fff;
        padding: 6vw;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        margin-top: 60px; 
    }

    .login-box h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #337ab7;
        font-weight: 600;
        font-size: 6vw;
        max-font-size: 24px;
    }

    .form-label {
        font-weight: 500;
        font-size: 4vw;
    }

    .form-control {
        font-size: 4vw;
        padding: 10px;
    }

    .btn-primary {
        background-color: #337ab7;
        border: none;
        transition: 0.3s;
        font-size: 4.5vw;
        padding: 10px;
    }

    .btn-primary:hover {
        background-color: #2e6da4;
    }

    .form-check-label,
    .forgot-link {
        font-size: 3.5vw;
    }

    @media (min-width: 768px) {
        .login-box {
            width: 100%;
            max-width: 500px;
            padding: 40px 30px;
        }

        .login-box h2 {
            font-size: 24px;
        }

        .form-label,
        .form-control,
        .btn-primary,
        .form-check-label,
        .forgot-link {
            font-size: 16px;
        }
    }
</style>

<div class="login-box">
    <h2>Login</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        @if ($message = Session::get('error'))
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @endif

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input id="username" type="text"
                   class="form-control @error('username') is-invalid @enderror"
                   name="username" value="{{ old('username') }}" required autofocus>
            @error('username')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   name="password" required>
            @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="mb-3 row align-items-center">
            <div class="col-6 d-flex align-items-center">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label ms-2" for="remember">
                    Remember Me
                </label>
            </div>
            <div class="col-6 d-flex justify-content-end align-items-center">
                <a href="{{ route('forgot_password') }}" class="forgot-link">Forgot Your Password?</a>
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                Login
            </button>
        </div>
    </form>
</div>
@endsection