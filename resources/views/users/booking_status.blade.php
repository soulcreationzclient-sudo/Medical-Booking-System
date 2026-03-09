<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 100%;
            max-width: 800px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Status Header */
        .status-header {
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .status-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
        }

        /* Status Colors */
        .status-header.pending {
            background: #1363c6;
        }
        .status-header.accepted {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .status-header.rejected {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        .status-header.cancelled {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }
        .status-header.unverified {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .status-icon {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulse 2s infinite;
            backdrop-filter: blur(10px);
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
            }
            50% {
                box-shadow: 0 0 0 20px rgba(255, 255, 255, 0);
            }
        }

        .status-icon svg {
            width: 45px;
            height: 45px;
            fill: white;
        }

        .status-text {
            font-size: 14px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 8px;
        }

        .status-label {
            font-size: 28px;
            font-weight: 800;
            color: white;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .status-message {
            margin-top: 15px;
            padding: 12px 25px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            display: inline-block;
            color: white;
            font-size: 14px;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }

        /* Card Body */
        .card-body {
            padding: 35px 30px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, #e5e7eb, transparent);
        }

        /* Info Items */
        .info-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 14px;
            transition: all 0.3s ease;
            animation: fadeInUp 0.5s ease-out both;
        }

        .info-item:nth-child(1) { animation-delay: 0.1s; }
        .info-item:nth-child(2) { animation-delay: 0.2s; }
        .info-item:nth-child(3) { animation-delay: 0.3s; }
        .info-item:nth-child(4) { animation-delay: 0.4s; }
        .info-item:nth-child(5) { animation-delay: 0.5s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .info-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .info-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-icon svg {
            width: 24px;
            height: 24px;
        }

        .info-icon.doctor {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        }
        .info-icon.doctor svg { fill: #2563eb; }

        .info-icon.hospital {
            background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
        }
        .info-icon.hospital svg { fill: #db2777; }

        .info-icon.date {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        }
        .info-icon.date svg { fill: #059669; }

        .info-icon.time {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }
        .info-icon.time svg { fill: #d97706; }

        .info-icon.patient {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        }
        .info-icon.patient svg { fill: #4f46e5; }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }

        /* Actions */
        .card-actions {
            padding: 0 30px 35px;
            display: flex;
            gap: 12px;
        }

        .btn {
            flex: 1;
            padding: 16px 20px;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn svg {
            width: 20px;
            height: 20px;
        }

        .btn-primary {
            background: #1363c6;
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-3px);
        }

        /* Booking ID */
        .booking-id {
            text-align: center;
            padding: 20px 30px;
            border-top: 1px dashed #e5e7eb;
            background: #fafafa;
        }

        .booking-id-label {
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .booking-id-value {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: 700;
            color: #6b7280;
            letter-spacing: 2px;
        }

        /* Timeline (Optional Enhancement) */
        .timeline {
            padding: 0 30px 25px;
        }

        .timeline-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .timeline-steps::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 25px;
            right: 25px;
            height: 3px;
            background: #e5e7eb;
            z-index: 0;
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .step-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .step-dot svg {
            width: 16px;
            height: 16px;
            fill: #9ca3af;
        }

        .step-dot.completed {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .step-dot.completed svg {
            fill: white;
        }

        .step-dot.active {
            background: #1363c6;
            animation: pulse 2s infinite;
        }

        .step-dot.active svg {
            fill: white;
        }

        .step-label {
            font-size: 11px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
        }

        .step-label.active {
            color: #667eea;
        }

        .step-label.completed {
            color: #10b981;
        }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .status-header {
                padding: 30px 20px;
            }

            .status-icon {
                width: 70px;
                height: 70px;
            }

            .status-icon svg {
                width: 35px;
                height: 35px;
            }

            .status-label {
                font-size: 22px;
            }

            .card-body,
            .card-actions {
                padding-left: 20px;
                padding-right: 20px;
            }

            .info-item {
                padding: 14px;
            }

            .info-icon {
                width: 42px;
                height: 42px;
            }

            .info-value {
                font-size: 14px;
            }

            .btn {
                padding: 14px 16px;
                font-size: 14px;
            }

            .timeline-steps::before {
                left: 15px;
                right: 15px;
            }

            .step-dot {
                width: 28px;
                height: 28px;
            }

            .step-label {
                font-size: 9px;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .card {
                box-shadow: none;
                border: 1px solid #e5e7eb;
            }

            .btn,
            .card-actions {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="card">
    <!-- Status Header -->
    <div class="status-header {{ $booking->status }}">
        <div class="status-icon">
            @if($booking->status === 'pending')
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                </svg>
            @elseif($booking->status === 'accepted')
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            @elseif($booking->status === 'rejected')
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/>
                </svg>
            @elseif($booking->status === 'cancelled')
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8 0-1.85.63-3.55 1.69-4.9L16.9 18.31C15.55 19.37 13.85 20 12 20zm6.31-3.1L7.1 5.69C8.45 4.63 10.15 4 12 4c4.42 0 8 3.58 8 8 0 1.85-.63 3.55-1.69 4.9z"/>
                </svg>
            @elseif($booking->status === 'unverified')
                <svg viewBox="0 0 24 24">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
            @endif
        </div>

        <div class="status-text">Appointment Status</div>
        <div class="status-label">{{ ucfirst(strtolower($booking->status)) }}</div>

        <div class="status-message">
            @if($booking->status === 'unverified')
                 Please check your email to verify
            @elseif($booking->status === 'pending')
                 Awaiting doctor's approval
            @elseif($booking->status === 'accepted')
                 Your appointment is confirmed!
            @elseif($booking->status === 'rejected')
                 This appointment was declined
            @elseif($booking->status === 'cancelled')
                 This appointment was cancelled
            @endif
        </div>
    </div>

    <!-- Progress Timeline -->
    <div class="timeline">
        <div class="timeline-steps">
            <div class="timeline-step">
                <div class="step-dot completed">
                    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                </div>
                <span class="step-label completed">Booked</span>
            </div>
            <div class="timeline-step">
                <div class="step-dot {{ in_array($booking->status, ['pending', 'accepted']) ? ($booking->status === 'pending' ? 'active' : 'completed') : '' }}">
                    @if($booking->status === 'accepted')
                        <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    @else
                        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                    @endif
                </div>
                <span class="step-label {{ $booking->status === 'pending' ? 'active' : ($booking->status === 'accepted' ? 'completed' : '') }}">Review</span>
            </div>
            <div class="timeline-step">
                <div class="step-dot {{ $booking->status === 'accepted' ? 'completed' : '' }}">
                    @if($booking->status === 'accepted')
                        <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    @else
                        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    @endif
                </div>
                <span class="step-label {{ $booking->status === 'accepted' ? 'completed' : '' }}">Confirmed</span>
            </div>
        </div>
    </div>

    <!-- Appointment Details -->
    <div class="card-body">
        <div class="section-title">Appointment Details</div>

        <div class="info-list">
            <div class="info-item">
                <!-- <div class="info-icon doctor">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div> -->
                <div class="info-content">
                    <div class="info-label">Doctor</div>
                    <div class="info-value">{{ ucfirst(strtolower($booking->doctor_name)) }}</div>
                </div>
            </div>

            <div class="info-item">
                <!-- <div class="info-icon hospital">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-1.99.9-1.99 2L3 19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-1 11h-4v4h-4v-4H6v-4h4V6h4v4h4v4z"/>
                    </svg>
                </div> -->
                <div class="info-content">
                    <div class="info-label">Hospital</div>
                    <div class="info-value">{{ ucfirst(strtolower($booking->hospital_name)) }}</div>
                </div>
            </div>

            <div class="info-item">
                <!-- <div class="info-icon date">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm-8 4H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/>
                    </svg>
                </div> -->
                <div class="info-content">
                    <div class="info-label">Date</div>
                    <div class="info-value">{{ $booking->booking_date }}</div>
                </div>
            </div>

            <div class="info-item">
                <!-- <div class="info-icon time">
                    <svg viewBox="0 0 24 24">
                        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                    </svg>
                </div> -->
                <div class="info-content">
                    <div class="info-label">Time</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}</div>
                </div>
            </div>

            <div class="info-item">
                <!-- <div class="info-icon patient">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2m0 10c2.7 0 5.8 1.29 6 2H6c.23-.72 3.31-2 6-2m0-12C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 10c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div> -->
                <div class="info-content">
                    <div class="info-label">Patient Name</div>
                    <div class="info-value">{{ ucwords(strtolower($booking->patient_name)) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card-actions">
        @if($booking->status === 'accepted')
            <button class="btn btn-primary" onclick="window.print()">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
                </svg>
                Print
            </button>
                {{-- <a href__="/calendar/add?booking={{ $booking->id }}" class="btn btn-secondary" style="text-decoration: none;">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm-8 4H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/>
                    </svg>
                    Add to Calendar
                </a> --}}
        @elseif($booking->status === 'pending')
            <a href__="/" class="btn btn-secondary" style="text-decoration: none;">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                Back to Home
            </a>
        @elseif(in_array($booking->status, ['rejected', 'cancelled']))
            <a href__="/book-appointment" class="btn btn-primary" style="text-decoration: none;">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                Book New Appointment
            </a>
        @elseif($booking->status === 'unverified')
            <button class="btn btn-primary" onclick="resendEmail()">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
                Resend Verification
            </button>
        @endif
    </div>

    <!-- Booking Reference -->
    <div class="booking-id">
        <div class="booking-id-label">Booking Reference</div>
        <div class="booking-id-value">{{ ($booking->action_token) }}</div>
    </div>
</div>

<script>
    // Resend verification email
    function resendEmail() {
        const btn = event.target.closest('button');
        btn.innerHTML = `
            <svg class="spin" viewBox="0 0 24 24" fill="currentColor" style="animation: spin 1s linear infinite;">
                <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/>
            </svg>
            Sending...
        `;

        // Simulate API call
        setTimeout(() => {
            btn.innerHTML = `
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
                Email Sent!
            `;
            btn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
        }, 2000);
    }

    // Add spin animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
</script>

</body>
</html>
