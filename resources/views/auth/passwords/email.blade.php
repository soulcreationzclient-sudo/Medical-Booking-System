@extends('layouts.app')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

<div style="
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#f4f7fb;
    font-family:'Open Sans',sans-serif;
    padding:20px;
">

    <div style="
        width:100%;
        max-width:600px;
        background:#ffffff;
        border-radius:14px;
        box-shadow:0 20px 40px rgba(0,0,0,0.08);
        padding:40px 35px;
        box-sizing:border-box;
    ">

        <!-- HEADER -->
        <h1 style="
            margin:0 0 8px 0;
            font-size:24px;
            font-weight:700;
            color:#1f2937;
            text-align:center;
        ">
            Reset Password
        </h1>

        <p style="
            margin:0 0 30px 0;
            font-size:14px;
            color:#6b7280;
            text-align:center;
            line-height:1.6;
        ">
            Enter your registered email address and we will send you a password reset link.
        </p>

        <!-- STATUS MESSAGE -->
        @if (session('status'))
            <div style="
                background:#ecfdf5;
                color:#065f46;
                padding:12px 14px;
                border-radius:8px;
                font-size:13px;
                margin-bottom:25px;
                text-align:center;
            ">
                {{ session('status') }}
            </div>
        @endif

        <!-- FORM -->
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- EMAIL -->
            <div style="margin-bottom:22px;">
                <label for="email" style="
                    display:block;
                    margin-bottom:6px;
                    font-size:13px;
                    font-weight:600;
                    color:#374151;
                ">
                    Email Address
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                    class="@error('email') is-invalid @enderror"
                    style="
                        width:100%;
                        padding:14px;
                        font-size:14px;
                        border:1px solid #d1d5db;
                        border-radius:8px;
                        box-sizing:border-box;
                    "
                >

                @error('email')
                    <div style="
                        color:#dc2626;
                        font-size:12px;
                        margin-top:6px;
                    ">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- SUBMIT -->
            <button type="submit" style="
                width:100%;
                padding:14px;
                font-size:15px;
                font-weight:600;
                color:#ffffff;
                background:#1363C6;
                border:none;
                border-radius:8px;
                cursor:pointer;
            ">
                Send Password Reset Link
            </button>
        </form>

    </div>
</div>

@endsection
