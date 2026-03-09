<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffff;
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 550px;
            background: #fff;
            padding: 40px 35px;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
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

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            background: #1363C6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4);
            }

            50% {
                box-shadow: 0 0 0 15px rgba(102, 126, 234, 0);
            }
        }

        .header-icon svg {
            width: 35px;
            height: 35px;
            fill: white;
        }

        .header h2 {
            font-size: 28px;
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .date-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #1363C6;
            padding: 10px 20px;
            border-radius: 30px;
            color: white;
            font-weight: 600;
            font-size: 15px;
            animation: fadeIn 0.8s ease-out 0.3s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .date-badge svg {
            width: 18px;
            height: 18px;
            fill: #5a67d8;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #4a5568;
            margin: 25px 0 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }

        /* TIME SLOTS */
        .slots-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            animation: fadeIn 0.8s ease-out 0.4s both;
        }

        .slot {
            flex: 1 1 calc(33.333% - 10px);
            min-width: 100px;
        }

        .slot input {
            display: none;
        }

        .slot label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px 10px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            color: #4a5568;
            background: #f8fafc;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .slot label::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: #1363C6;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.4s ease, height 0.4s ease;
            z-index: 0;
        }

        .slot label span {
            position: relative;
            z-index: 1;
        }

        .slot label:hover {
            border-color: #1363C6;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .slot input:checked+label {
            border-color: #1363C6;
            color: white;
        }

        .slot input:checked+label::before {
            width: 300px;
            height: 300px;
        }

        .slot input:checked+label {
            animation: selectPop 0.3s ease;
        }

        @keyframes selectPop {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(0.95);
            }

            100% {
                transform: scale(1);
            }
        }

        .no-slots {
            width: 100%;
            text-align: center;
            padding: 30px;
            background: #fef2f2;
            border-radius: 12px;
            color: #dc2626;
            font-weight: 500;
        }

        /* FORM INPUTS */
        .form-group {
            margin-bottom: 15px;
            animation: fadeIn 0.8s ease-out 0.5s both;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper svg {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            fill: #a0aec0;
            transition: fill 0.3s ease;
        }

        .form-input {
            width: 100%;
            padding: 16px 16px 16px 50px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            color: #2d3748;
            background: #f8fafc;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input::placeholder {
            color: #a0aec0;
        }

        .form-input:focus {
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-input:focus+svg,
        .input-wrapper:focus-within svg {
            fill: #667eea;
        }

        /* SUBMIT BUTTON */
        .submit-btn {
            width: 100%;
            padding: 18px;
            margin-top: 25px;
            border: none;
            border-radius: 14px;
            background: #1363C6;
            color: white;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.8s ease-out 0.6s both;
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
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        /* FOOTER INFO */
        .footer-info {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            animation: fadeIn 0.8s ease-out 0.7s both;
        }

        .footer-info p {
            font-size: 13px;
            color: #718096;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .footer-info svg {
            width: 16px;
            height: 16px;
            fill: #48bb78;
        }

        /* RESPONSIVE */
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .container {
                padding: 30px 20px;
            }

            .header h2 {
                font-size: 24px;
            }

            .header-icon {
                width: 60px;
                height: 60px;
            }

            .slot {
                flex: 1 1 calc(50% - 10px);
            }

            .slot label {
                padding: 12px 8px;
                font-size: 13px;
            }

            .form-input {
                padding: 14px 14px 14px 45px;
                font-size: 14px;
            }

            .submit-btn {
                padding: 16px;
                font-size: 16px;
            }
        }

        /* Loading State */
        .submit-btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .submit-btn.loading::after {
            content: '';
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            display: inline-block;
            margin-left: 10px;
            animation: spin 1s linear infinite;
            vertical-align: middle;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- HEADER -->
        <div class="header">
            <div class="header-icon">
                <svg viewBox="0 0 24 24">
                    <path
                        d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm-8 4H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z" />
                </svg>
            </div>
            <h2>Book Appointment</h2>
            <div class="date-badge">
                <!-- <svg viewBox="0 0 24 24">
                    <path
                        d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z" />
                </svg> -->
                {{ $date }}
            </div>
        </div>

        <form id="bookingForm">
            @csrf

            <input type="hidden" name="booking_date" value="{{ $date }}">

            <!-- TIME SLOTS -->
            <div class="section-title">Select Time Slot</div>

            <div class="slots-container">
                @forelse($slots as $index => $slot)
                    <div class="slot" style="animation-delay: {{ $index * 0.05 }}s">
                        <input type="radio" name="start_time" id="slot_{{ $slot['start'] }}"
                            value="{{ $slot['start'] }}" required>
                        <label for="slot_{{ $slot['start'] }}">
                            <span>{{ \Carbon\Carbon::parse($slot['start'])->format('h:i A') }}</span>
                        </label>
                    </div>
                @empty
                    <div class="no-slots">
                        <svg style="width:40px;height:40px;fill:#dc2626;margin-bottom:10px" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                        </svg>
                        <p>No slots available for this date</p>
                    </div>
                @endforelse
            </div>

            <!-- PATIENT DETAILS -->
            <div class="section-title">Your Details</div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" class="form-input" name="patient_name" placeholder="Full Name" required>
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="email" class="form-input" name="patient_email" placeholder="Email Address" required>
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                    </svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="tel" class="form-input" name="patient_phone" placeholder="Phone Number" required value="{{$phone_no}}">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                    </svg>
                </div>
            </div>
            <div class="form-group">
                <div class="input-wrapper">
                    <input type="number" class="form-input" name="age" placeholder="Age" min="0"
                        max="120">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
            </div>
            <div class="form-group">
                <div class="input-wrapper">
                    <textarea class="form-input" name="cause" placeholder="Reason for visit / Symptoms" rows="3"
                        style="padding-left:50px; resize:none;"></textarea>
                    <svg viewBox="0 0 24 24">
                        <path d="M3 5v14h18V5H3zm16 12H5V7h14v10zm-2-2H7v-2h10v2zm0-4H7V9h10v2z" />
                    </svg>
                </div>
            </div>


            <button type="submit" class="submit-btn" id="submitBtn">
                Confirm Booking
            </button>

        </form>

        <div class="footer-info">
            <p>
                <svg viewBox="0 0 24 24">
                    <path
                        d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
                </svg>
                Your information is secure and encrypted
            </p>
        </div>

    </div>

    <script>
        // Add loading state on form submit
        document.getElementById('bookingForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.textContent = 'Booking';
        });

        // Add ripple effect on slot selection
        document.querySelectorAll('.slot label').forEach(label => {
            label.addEventListener('click', function(e) {
                // Create ripple
                const ripple = document.createElement('span');
                ripple.style.cssText = `
                position: absolute;
                background: rgba(102, 126, 234, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;

                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';

                this.appendChild(ripple);

                setTimeout(() => ripple.remove(), 600);
            });
        });

        // Add keyframes for ripple
        const style = document.createElement('style');
        style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
        document.head.appendChild(style);
    </script>
    <script>
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.textContent = 'Booking...';

            fetch("{{ route('booking.ajax.store', $doctorId) }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: new FormData(form)
                })
                .then(res => res.json())
                .then(data => {
                    btn.classList.remove('loading');

                    if (data.success) {
                        form.innerHTML = `
                <div style="text-align:center;padding:30px">
                    <h2 style="color:#4CAF50">Booking Submitted</h2>
                    <p>Your booking code:</p>
                    <h1 style="letter-spacing:2px">${data.booking_code}</h1>
                    <p>Please keep this code to check your status.</p>

                    <a href="/booking/status/${data.booking_code}"
                       style="display:inline-block;margin-top:20px;
                              background:#1363c6;color:#fff;
                              padding:12px 25px;border-radius:10px;
                              text-decoration:none;font-weight:600">
                        Check Booking Status
                    </a>
                </div>
            `;
                    } else {
                        btn.textContent = 'Confirm Booking';
                        alert('Something went wrong. Try again.');
                    }
                })
                .catch(() => {
                    btn.classList.remove('loading');
                    btn.textContent = 'Confirm Booking';
                    alert('Network error. Please try again.');
                });
        });
    </script>

</body>

</html>
