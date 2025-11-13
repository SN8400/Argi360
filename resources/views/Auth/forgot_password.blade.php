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
    <h2>Request Reset Password</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        @if ($message = Session::get('error'))
            <div class="alert alert-danger">
                {{ $message }}
            </div>
        @endif

        <div class="mb-3">
            <label for="mail" class="form-label">E-mail</label>
            <input id="mail" type="text"
                   class="form-control @error('mail') is-invalid @enderror"
                   name="mail" value="{{ old('mail') }}" required autofocus>
            @error('mail')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                Request Reset Password
            </button>
        </div>
    </form>
</div>
@endsection