{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app2')

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
        max-width:1100px;
        background:#ffffff;
        display:flex;
        flex-wrap:wrap;
        border-radius:14px;
        overflow:hidden;
        box-shadow:0 20px 40px rgba(0,0,0,0.08);
    ">

        <!-- LEFT PANEL -->
        <div style="
            flex:1;
            min-width:280px;
            background:#1363C6;
            color:#ffffff;
            padding:50px 40px;
            box-sizing:border-box;
        ">
            <div style="margin-bottom:20px;">
    <img src="{{asset('logo/Gemini_Generated_Image_umceniumceniumce.png')}}"
         alt="Healthcare Logo"
         style="
            width:180px;
            height:auto;
            display:block;
            background:#ffffff;
            padding:12px 18px;
            border-radius:14px;
            box-shadow:0 6px 18px rgba(0,0,0,0.15);
         ">
</div>
            <h2 style="
                margin:0 0 12px 0;
                font-size:26px;
                font-weight:700;
            ">
                HealthCare System
            </h2>

            <p style="
                font-size:15px;
                line-height:1.6;
                margin:0 0 25px 0;
                opacity:0.95;
            ">
                Secure medical booking and patient management platform.
            </p>

            <ul style="
                padding-left:18px;
                margin:0;
                font-size:14px;
                line-height:1.8;
            ">
                <li>Appointment Booking</li>
                <li>Patient Medical Records</li>
                <li>Doctor & Staff Access</li>
                <li>Secure Healthcare Data</li>


            </ul>
        </div>

        <!-- RIGHT PANEL -->
        <div style="
            flex:1;
            min-width:280px;
            padding:50px 40px;
            box-sizing:border-box;
        ">

            <div style="margin-bottom:30px;">
                <h1 style="
                    margin:0 0 6px 0;
                    font-size:24px;
                    font-weight:700;
                    color:#1f2937;
                ">
                    Sign In
                </h1>

                <p style="
                    margin:0;
                    font-size:14px;
                    color:#6b7280;
                ">
                    Please sign in to access your account
                </p>
            </div>

            <form method="POST" action="{{ route('login') }}">
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
                        <div style="color:#dc2626;font-size:12px;margin-top:6px;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- PASSWORD -->
<div style="margin-bottom:22px;">
    <label for="password" style="
        display:block;
        margin-bottom:6px;
        font-size:13px;
        font-weight:600;
        color:#374151;
    ">
        Password
    </label>

    <div style="position:relative;">
        <input
            id="password"
            type="password"
            name="password"
            required
            autocomplete="current-password"
            class="@error('password') is-invalid @enderror"
            style="
                width:100%;
                padding:14px 44px 14px 14px;
                font-size:14px;
                border:1px solid #d1d5db;
                border-radius:8px;
                box-sizing:border-box;
            "
        >

        <!-- TOGGLE ICON -->
        <span
            onclick="
                const p = document.getElementById('password');
                this.innerText = p.type === 'password' ? '👁' : '⌣';
                p.type = p.type === 'password' ? 'text' : 'password';
            "
            style="
                position:absolute;
                right:12px;
                top:50%;
                transform:translateY(-50%);
                cursor:pointer;
                font-size:16px;
                color:#6b7280;
                user-select:none;
            "
        >
            ⌣
        </span>
    </div>

    @error('password')
        <div style="color:#dc2626;font-size:12px;margin-top:6px;">
            {{ $message }}
        </div>
    @enderror
</div>


                <!-- OPTIONS -->
                <div style="
                    display:flex;
                    flex-wrap:wrap;
                    justify-content:space-between;
                    align-items:center;
                    margin-bottom:25px;
                    gap:12px;
                    font-size:13px;
                ">
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            {{ old('remember') ? 'checked' : '' }}
                            style="accent-color:#1363C6;"
                        >
                        Remember Me
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           style="color:#1363C6;text-decoration:none;font-weight:600;">
                            Forgot Password?
                        </a>
                    @endif
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
                    Sign In
                </button>
            </form>

            @if (Route::has('register'))
                <p style="
                    margin-top:28px;
                    font-size:13px;
                    text-align:center;
                    color:#6b7280;
                ">
                    Don’t have an account?
                    <a href="{{ route('register') }}"
                       style="color:#1363C6;font-weight:600;text-decoration:none;">
                        Create Account
                    </a>
                </p>
            @endif

        </div>
    </div>
</div>

@endsection
