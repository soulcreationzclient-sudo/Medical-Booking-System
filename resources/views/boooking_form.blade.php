{{-- resources/views/booking_form.blade.php --}}
@extends('layouts.app2')
@section('title','Booking Form')
@section('content')

<style>
    .booking-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #e0f7fa 0%, #e8f5e9 50%, #f3e5f5 100%);
        padding: 40px 20px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        position: relative;
        overflow: hidden;
    }

    .booking-page::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 70%;
        height: 100%;
        background: radial-gradient(circle, rgba(0, 188, 212, 0.08) 0%, transparent 70%);
        animation: float 20s ease-in-out infinite;
    }

    .booking-page::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 50%;
        height: 80%;
        background: radial-gradient(circle, rgba(156, 39, 176, 0.06) 0%, transparent 70%);
        animation: float 15s ease-in-out infinite reverse;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-30px) rotate(5deg); }
    }

    .booking-container {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 40px;
        position: relative;
        z-index: 1;
    }

    .booking-illustration {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 40px;
    }

    .illustration-svg {
        width: 100%;
        max-width: 400px;
        height: auto;
    }

    .booking-tagline {
        text-align: center;
        margin-top: 30px;
    }

    .booking-tagline h1 {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #00897b 0%, #7b1fa2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 12px;
        letter-spacing: -0.02em;
    }

    .booking-tagline p {
        color: #5f6368;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .booking-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 32px;
        padding: 48px;
        box-shadow:
            0 4px 6px -1px rgba(0, 0, 0, 0.05),
            0 20px 50px -12px rgba(0, 0, 0, 0.1),
            0 0 0 1px rgba(255, 255, 255, 0.5) inset;
        position: relative;
        overflow: hidden;
    }

    .booking-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #00bcd4, #9c27b0, #00bcd4);
        background-size: 200% 100%;
        animation: shimmer 3s linear infinite;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .form-header {
        text-align: center;
        margin-bottom: 36px;
    }

    .form-header h2 {
        font-size: 1.75rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 8px;
    }

    .form-header p {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .booking-form {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        position: relative;
    }

    .form-group-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        z-index: 2;
        width: 20px;
        height: 20px;
    }

    .form-group.focused .form-group-icon {
        color: #00897b;
    }

    .form-input {
        width: 100%;
        padding: 18px 18px 18px 52px;
        font-size: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.9);
        color: #1f2937;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
        font-family: inherit;
        box-sizing: border-box;
    }

    .form-input::placeholder {
        color: #9ca3af;
    }

    .form-input:hover {
        border-color: #d1d5db;
        background: #fff;
    }

    .form-input:focus {
        border-color: #00897b;
        background: #fff;
        box-shadow:
            0 0 0 4px rgba(0, 137, 123, 0.1),
            0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .form-select {
        width: 100%;
        padding: 18px 48px 18px 52px;
        font-size: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.9);
        color: #1f2937;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
        font-family: inherit;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 20px;
        box-sizing: border-box;
    }

    .form-select:hover {
        border-color: #d1d5db;
        background-color: #fff;
    }

    .form-select:focus {
        border-color: #00897b;
        background-color: #fff;
        box-shadow:
            0 0 0 4px rgba(0, 137, 123, 0.1),
            0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .form-textarea {
        width: 100%;
        padding: 18px 18px 18px 52px;
        font-size: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.9);
        color: #1f2937;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
        font-family: inherit;
        resize: none;
        min-height: 120px;
        box-sizing: border-box;
    }

    .form-textarea::placeholder {
        color: #9ca3af;
    }

    .form-textarea:hover {
        border-color: #d1d5db;
        background: #fff;
    }

    .form-textarea:focus {
        border-color: #00897b;
        background: #fff;
        box-shadow:
            0 0 0 4px rgba(0, 137, 123, 0.1),
            0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .form-group.textarea-group .form-group-icon {
        top: 24px;
        transform: none;
    }

    .submit-btn {
        width: 100%;
        padding: 18px 32px;
        font-size: 1.1rem;
        font-weight: 600;
        color: #fff;
        background: linear-gradient(135deg, #00897b 0%, #00acc1 100%);
        border: none;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin-top: 8px;
        position: relative;
        overflow: hidden;
    }

    .submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow:
            0 10px 30px -10px rgba(0, 137, 123, 0.5),
            0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .submit-btn:hover::before {
        left: 100%;
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .submit-btn svg {
        width: 20px;
        height: 20px;
    }

    .floating-elements {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        pointer-events: none;
        overflow: hidden;
    }

    .floating-heart {
        position: absolute;
        opacity: 0.15;
        animation: floatHeart 6s ease-in-out infinite;
    }

    .floating-heart:nth-child(1) {
        top: 10%;
        left: 5%;
        animation-delay: 0s;
    }

    .floating-heart:nth-child(2) {
        top: 60%;
        right: 8%;
        animation-delay: 2s;
    }

    .floating-heart:nth-child(3) {
        bottom: 15%;
        left: 10%;
        animation-delay: 4s;
    }

    @keyframes floatHeart {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-15px) scale(1.1); }
    }

    /* Mobile Responsive Styles */
    @media (max-width: 900px) {
        .booking-container {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .booking-illustration {
            padding: 20px;
        }

        .illustration-svg {
            max-width: 280px;
        }

        .booking-tagline h1 {
            font-size: 2rem;
        }

        .booking-card {
            padding: 32px 24px;
            border-radius: 24px;
        }
    }

    @media (max-width: 600px) {
        .booking-page {
            padding: 20px 15px;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .booking-tagline h1 {
            font-size: 1.6rem;
        }

        .booking-tagline p {
            font-size: 0.95rem;
        }

        .form-header h2 {
            font-size: 1.4rem;
        }

        .form-input,
        .form-select,
        .form-textarea {
            padding: 16px 16px 16px 48px;
            font-size: 0.95rem;
            border-radius: 12px;
        }

        .form-group-icon {
            left: 14px;
            width: 18px;
            height: 18px;
        }

        .submit-btn {
            padding: 16px 24px;
            font-size: 1rem;
            border-radius: 12px;
        }

        .booking-card {
            padding: 28px 20px;
            border-radius: 20px;
        }

        .illustration-svg {
            max-width: 220px;
        }
    }

    @media (max-width: 380px) {
        .booking-page {
            padding: 15px 10px;
        }

        .booking-card {
            padding: 24px 16px;
        }

        .booking-tagline h1 {
            font-size: 1.4rem;
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-header h2 {
            font-size: 1.25rem;
        }
    }
</style>

<div class="booking-page">
    <!-- Floating Decorative Elements -->
    <div class="floating-elements">
        <svg class="floating-heart" width="40" height="40" viewBox="0 0 24 24" fill="#00897b">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <svg class="floating-heart" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#9c27b0" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </svg>
        <svg class="floating-heart" width="32" height="32" viewBox="0 0 24 24" fill="#00acc1">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
    </div>

    <div class="booking-container">
        <!-- Left Side - Illustration -->
        <div class="booking-illustration">
            <svg class="illustration-svg" viewBox="0 0 400 350" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Background Circle -->
                <circle cx="200" cy="175" r="140" fill="url(#bgGradient)" opacity="0.3"/>

                <!-- Doctor Shadow -->
                <ellipse cx="200" cy="310" rx="80" ry="20" fill="#e0e0e0" opacity="0.5"/>

                <!-- Body - Lab Coat -->
                <path d="M140 200 L140 280 L260 280 L260 200 Q200 220 140 200" fill="white" stroke="#00897b" stroke-width="2"/>
                <path d="M140 200 L160 280" stroke="#e0e0e0" stroke-width="1"/>
                <path d="M260 200 L240 280" stroke="#e0e0e0" stroke-width="1"/>

                <!-- Coat Buttons -->
                <rect x="190" y="210" width="20" height="60" rx="2" fill="#f0f0f0"/>
                <circle cx="200" cy="225" r="4" fill="#00897b"/>
                <circle cx="200" cy="245" r="4" fill="#00897b"/>
                <circle cx="200" cy="265" r="4" fill="#00897b"/>

                <!-- Stethoscope -->
                <path d="M170 200 Q150 220 155 250" stroke="#4a4a4a" stroke-width="3" fill="none"/>
                <circle cx="155" cy="255" r="8" fill="#4a4a4a"/>
                <circle cx="155" cy="255" r="4" fill="#9c27b0"/>

                <!-- Head -->
                <circle cx="200" cy="150" r="50" fill="#ffd5c8"/>

                <!-- Hair -->
                <path d="M150 140 Q150 100 200 100 Q250 100 250 140 Q240 120 200 125 Q160 120 150 140" fill="#4a3728"/>

                <!-- Face -->
                <circle cx="185" cy="145" r="5" fill="#4a3728"/>
                <circle cx="215" cy="145" r="5" fill="#4a3728"/>
                <path d="M190 165 Q200 175 210 165" stroke="#4a3728" stroke-width="2" fill="none" stroke-linecap="round"/>

                <!-- Glasses -->
                <rect x="173" y="138" width="24" height="16" rx="8" stroke="#4a4a4a" stroke-width="2" fill="none"/>
                <rect x="203" y="138" width="24" height="16" rx="8" stroke="#4a4a4a" stroke-width="2" fill="none"/>
                <line x1="197" y1="146" x2="203" y2="146" stroke="#4a4a4a" stroke-width="2"/>

                <!-- Arms -->
                <path d="M140 205 L100 250 L120 280" stroke="#ffd5c8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M260 205 L300 250 L280 280" stroke="#ffd5c8" stroke-width="20" stroke-linecap="round" stroke-linejoin="round"/>

                <!-- Clipboard -->
                <rect x="265" y="240" width="40" height="55" rx="4" fill="#f5f5f5" stroke="#00897b" stroke-width="2"/>
                <rect x="275" y="235" width="20" height="10" rx="2" fill="#00897b"/>
                <line x1="272" y1="255" x2="298" y2="255" stroke="#e0e0e0" stroke-width="2"/>
                <line x1="272" y1="265" x2="298" y2="265" stroke="#e0e0e0" stroke-width="2"/>
                <line x1="272" y1="275" x2="290" y2="275" stroke="#e0e0e0" stroke-width="2"/>
                <line x1="272" y1="285" x2="295" y2="285" stroke="#9c27b0" stroke-width="2"/>

                <!-- Medical Cross Badge -->
                <circle cx="250" cy="210" r="12" fill="#00897b"/>
                <rect x="247" y="203" width="6" height="14" rx="1" fill="white"/>
                <rect x="243" y="207" width="14" height="6" rx="1" fill="white"/>

                <!-- Floating Hearts Animation -->
                <g opacity="0.6">
                    <path d="M80 100 C80 90 95 90 95 100 C95 90 110 90 110 100 C110 120 95 130 95 130 C95 130 80 120 80 100" fill="#ff6b9d">
                        <animateTransform attributeName="transform" type="translate" values="0,0; 0,-10; 0,0" dur="3s" repeatCount="indefinite"/>
                    </path>
                    <path d="M300 80 C300 73 310 73 310 80 C310 73 320 73 320 80 C320 93 310 100 310 100 C310 100 300 93 300 80" fill="#00bcd4">
                        <animateTransform attributeName="transform" type="translate" values="0,0; 0,-8; 0,0" dur="2.5s" repeatCount="indefinite"/>
                    </path>
                    <path d="M320 180 C320 175 327 175 327 180 C327 175 334 175 334 180 C334 190 327 195 327 195 C327 195 320 190 320 180" fill="#9c27b0">
                        <animateTransform attributeName="transform" type="translate" values="0,0; 0,-6; 0,0" dur="4s" repeatCount="indefinite"/>
                    </path>
                </g>

                <!-- Pulse Line -->
                <path d="M50 300 L80 300 L90 280 L100 320 L110 290 L120 310 L130 300 L350 300"
                      stroke="#00897b" stroke-width="2" fill="none" opacity="0.3">
                    <animate attributeName="stroke-dasharray" values="0,400;400,0" dur="2s" repeatCount="indefinite"/>
                </path>

                <defs>
                    <linearGradient id="bgGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#00bcd4"/>
                        <stop offset="100%" stop-color="#9c27b0"/>
                    </linearGradient>
                </defs>
            </svg>

            <div class="booking-tagline">
                <h1>Your Health, Our Priority</h1>
                <p>Book an appointment with our expert doctors in just a few clicks</p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="booking-card">
            <div class="form-header">
                <h2>Book Your Appointment</h2>
                <p>Fill in your details and we'll get back to you shortly</p>
            </div>

            <form class="booking-form" action="" method="POST">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <svg class="form-group-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <input type="text" name="name" class="form-input" placeholder="Your Full Name" required>
                    </div>

                    <div class="form-group">
                        <svg class="form-group-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <input type="tel" name="phone" class="form-input" placeholder="Phone Number" required>
                    </div>
                </div>

                <div class="form-group">
                    <svg class="form-group-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4.8 2.3A.3.3 0 1 0 5 2.9 2 2 0 0 1 5 6a2 2 0 0 1 0 4 2 2 0 0 1 0 4 2 2 0 0 1 0 4 2 2 0 0 1 0 4 .3.3 0 1 0-.2.6h.2a4 4 0 0 0 4-4V6a4 4 0 0 0-4-4z"/>
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 8v8"/>
                        <path d="M8 12h8"/>
                    </svg>
                    <select name="doctor" class="form-select" required>
                        <option value="">Select a Doctor</option>
                        {{-- @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }} - {{ $doctor->specialty }}</option>
                        @endforeach --}}
                        {{-- Static options if no dynamic data --}}
                        {{--
                        <option value="1">Dr. Sarah Mitchell - Cardiologist</option>
                        <option value="2">Dr. James Chen - Neurologist</option>
                        <option value="3">Dr. Emily Roberts - Dermatologist</option>
                        <option value="4">Dr. Michael Foster - Orthopedist</option>
                        <option value="5">Dr. Lisa Anderson - Pediatrician</option>
                        --}}
                    </select>
                </div>

                <div class="form-group">
                    <svg class="form-group-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <input type="text" name="cause" class="form-input" placeholder="Reason for Visit" required>
                </div>

                <div class="form-group">
                    <svg class="form-group-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <select name="availability" class="form-select" required>
                        <option value="">Select Available Slot</option>
                        {{-- @foreach($availabilitySlots as $slot)
                            <option value="{{ $slot->id }}">{{ $slot->day }} • {{ $slot->time }}</option>
                        @endforeach --}}
                        {{-- Static options if no dynamic data --}}
                        {{--
                        <option value="1">Monday • 9:00 AM - 12:00 PM</option>
                        <option value="2">Monday • 2:00 PM - 5:00 PM</option>
                        <option value="3">Tuesday • 10:00 AM - 1:00 PM</option>
                        <option value="4">Wednesday • 9:00 AM - 12:00 PM</option>
                        <option value="5">Thursday • 3:00 PM - 6:00 PM</option>
                        <option value="6">Friday • 11:00 AM - 2:00 PM</option>
                        --}}
                    </select>
                </div>

                <div class="form-group textarea-group">
                    <svg class="form-group-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    <textarea name="details" class="form-textarea" placeholder="Additional Details (symptoms, medical history, etc.)"></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                    Book Appointment
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Add focus class for icon color change
    document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('.form-group').classList.add('focused');
        });
        input.addEventListener('blur', function() {
            this.closest('.form-group').classList.remove('focused');
        });
    });
</script>

@endsection
