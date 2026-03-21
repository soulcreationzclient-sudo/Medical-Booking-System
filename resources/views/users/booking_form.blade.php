<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4ff;
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 600px;
            background: #fff;
            padding: 40px 35px;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.12);
            margin: 20px auto;
        }

        .header { text-align: center; margin-bottom: 30px; }

        .header-icon {
            width: 70px; height: 70px;
            background: #1363C6;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(19,99,198,0.4); }
            50%      { box-shadow: 0 0 0 15px rgba(19,99,198,0); }
        }

        .header-icon svg { width: 35px; height: 35px; fill: white; }
        .header h2 { font-size: 26px; color: #2d3748; font-weight: 700; margin-bottom: 10px; }

        .date-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: #1363C6; padding: 10px 20px;
            border-radius: 30px; color: white;
            font-weight: 600; font-size: 15px;
        }

        /* SECTION TITLES */
        .section-title {
            font-size: 15px; font-weight: 700;
            color: #4a5568; margin: 25px 0 15px;
            display: flex; align-items: center; gap: 10px;
        }
        .section-title::before {
            content: '';
            width: 4px; height: 20px;
            background: linear-gradient(135deg, #1363C6 0%, #4a90e2 100%);
            border-radius: 4px;
        }

        /* TIME SLOTS */
        .slots-container { display: flex; flex-wrap: wrap; gap: 10px; }

        .slot { flex: 1 1 calc(33.333% - 10px); min-width: 100px; }
        .slot input { display: none; }
        .slot label {
            display: flex; align-items: center; justify-content: center;
            padding: 14px 10px; border: 2px solid #e2e8f0;
            border-radius: 12px; cursor: pointer;
            font-weight: 600; font-size: 14px; color: #4a5568;
            background: #f8fafc; transition: all 0.3s ease;
            position: relative; overflow: hidden;
        }
        .slot label::before {
            content: ''; position: absolute;
            top: 50%; left: 50%; width: 0; height: 0;
            background: #1363C6; border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.4s ease, height 0.4s ease; z-index: 0;
        }
        .slot label span { position: relative; z-index: 1; }
        .slot label:hover { border-color: #1363C6; transform: translateY(-2px); }
        .slot input:checked + label { border-color: #1363C6; color: white; }
        .slot input:checked + label::before { width: 300px; height: 300px; }

        /* TWO COLUMN GRID */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-grid .full-width { grid-column: 1 / -1; }

        /* FORM INPUTS */
        .form-group { margin-bottom: 0; }

        .input-wrapper { position: relative; }
        .input-wrapper svg {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            width: 18px; height: 18px; fill: #a0aec0; pointer-events: none;
        }
        .input-wrapper.textarea-wrap svg { top: 16px; transform: none; }

        .form-input {
            width: 100%; padding: 13px 13px 13px 44px;
            border: 2px solid #e2e8f0; border-radius: 12px;
            font-size: 14px; color: #2d3748;
            background: #f8fafc; transition: all 0.3s ease; outline: none;
        }
        .form-input::placeholder { color: #a0aec0; }
        .form-input:focus {
            border-color: #1363C6; background: #fff;
            box-shadow: 0 0 0 3px rgba(19,99,198,0.1);
        }
        select.form-input { cursor: pointer; }
        textarea.form-input { resize: none; }

        /* PRE-FILL BANNER */
        #prefillBanner {
            display: none;
            background: #e8f5e9; border: 1px solid #81c784;
            color: #2e7d32; border-radius: 10px;
            padding: 10px 16px; font-size: 13px;
            margin-bottom: 16px; align-items: center; gap: 8px;
        }
        #prefillBanner.show { display: flex; }

        /* SUBMIT BUTTON */
        .submit-btn {
            width: 100%; padding: 17px; margin-top: 25px;
            border: none; border-radius: 14px;
            background: #1363C6; color: white;
            font-size: 16px; font-weight: 700; cursor: pointer;
            transition: all 0.3s ease;
        }
        .submit-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(19,99,198,0.3); }
        .submit-btn.loading { pointer-events: none; opacity: 0.8; }
        .submit-btn.loading::after {
            content: ''; width: 18px; height: 18px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white; border-radius: 50%;
            display: inline-block; margin-left: 10px;
            animation: spin 1s linear infinite; vertical-align: middle;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .footer-info {
            text-align: center; margin-top: 25px; padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .footer-info p {
            font-size: 13px; color: #718096;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .footer-info svg { width: 16px; height: 16px; fill: #48bb78; }

        .no-slots {
            width: 100%; text-align: center; padding: 30px;
            background: #fef2f2; border-radius: 12px;
            color: #dc2626; font-weight: 500;
        }

        @media (max-width: 520px) {
            .container { padding: 25px 16px; }
            .form-grid { grid-template-columns: 1fr; }
            .slot { flex: 1 1 calc(50% - 10px); }
        }
    </style>
</head>

<body>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div class="header-icon">
            <svg viewBox="0 0 24 24">
                <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm-8 4H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/>
            </svg>
        </div>
        <h2>Book Appointment</h2>
        <div class="date-badge">{{ $date }}</div>
    </div>

    <!-- PRE-FILL BANNER -->
    <div id="prefillBanner">
        ✅ We found your details — form has been pre-filled!
    </div>

    <form id="bookingForm">
        @csrf
        <input type="hidden" name="booking_date" value="{{ $date }}">

        <!-- TIME SLOTS -->
        <div class="section-title">Select Time Slot</div>
        <div class="slots-container">
            @forelse($slots as $index => $slot)
                <div class="slot">
                    <input type="radio" name="start_time" id="slot_{{ $slot['start'] }}"
                           value="{{ $slot['start'] }}" required>
                    <label for="slot_{{ $slot['start'] }}">
                        <span>{{ \Carbon\Carbon::parse($slot['start'])->format('h:i A') }}</span>
                    </label>
                </div>
            @empty
                <div class="no-slots">No slots available for this date</div>
            @endforelse
        </div>

        <!-- PERSONAL INFO -->
        <div class="section-title">Personal Information</div>
        <div class="form-grid">

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" class="form-input" name="patient_name" id="patient_name"
                           placeholder="Full Name" required>
                    <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" class="form-input" name="ic_passport_no" id="ic_passport_no"
                           placeholder="IC / Passport No">
                    <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8h16v10zM6 10h2v2H6zm0 4h8v2H6zm10 0h2v2h-2zm0-4h2v2h-2z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="tel" class="form-input" name="patient_phone" id="patient_phone"
                           placeholder="Phone Number" required value="{{ $phone_no }}">
                    <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="email" class="form-input" name="patient_email" id="patient_email"
                           placeholder="Email Address">
                    <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="number" class="form-input" name="age" id="age"
                           placeholder="Age" min="0" max="120">
                    <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="date" class="form-input" name="dob" id="dob" placeholder="Date of Birth">
                    <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <select class="form-input" name="gender" id="gender" style="padding-left:44px">
                        <option value="">Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <select class="form-input" name="blood_type" id="blood_type" style="padding-left:44px">
                        <option value="">Blood Type</option>
                        <option value="A+">A+</option><option value="A-">A-</option>
                        <option value="B+">B+</option><option value="B-">B-</option>
                        <option value="AB+">AB+</option><option value="AB-">AB-</option>
                        <option value="O+">O+</option><option value="O-">O-</option>
                    </select>
                    <svg viewBox="0 0 24 24"><path d="M12 2C9.24 2 7 4.24 7 7c0 3.75 5 11 5 11s5-7.25 5-11c0-2.76-2.24-5-5-5zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <select class="form-input" name="marital_status" id="marital_status" style="padding-left:44px">
                        <option value="">Marital Status</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="divorced">Divorced</option>
                        <option value="widowed">Widowed</option>
                    </select>
                    <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" class="form-input" name="nationality" id="nationality"
                           placeholder="Nationality" value="Malaysian">
                    <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
                </div>
            </div>

        </div>

        <!-- EMERGENCY CONTACT -->
        <div class="section-title">Emergency Contact</div>
        <div class="form-grid">

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" class="form-input" name="emergency_contact_name" id="emergency_contact_name"
                           placeholder="Emergency Contact Name">
                    <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="tel" class="form-input" name="emergency_contact_no" id="emergency_contact_no"
                           placeholder="Emergency Contact No">
                    <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                </div>
            </div>

        </div>

        <!-- ADDRESS -->
        <div class="section-title">Address</div>
        <div class="form-grid">

            <div class="form-group full-width">
                <div class="input-wrapper textarea-wrap">
                    <textarea class="form-input" name="address" id="address" rows="2"
                              placeholder="Address"></textarea>
                    <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" class="form-input" name="city" id="city" placeholder="City">
                    <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" class="form-input" name="state" id="state" placeholder="State">
                    <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" class="form-input" name="postcode" id="postcode" placeholder="Postcode">
                    <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z"/></svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" class="form-input" name="country" id="country" placeholder="Country" value="Malaysia">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg>
                </div>
            </div>

        </div>

        <!-- VISIT REASON -->
        <div class="section-title">Visit Details</div>
        <div class="form-group">
            <div class="input-wrapper textarea-wrap">
                <textarea class="form-input" name="cause" id="cause" rows="3"
                          placeholder="Reason for visit / Symptoms" style="padding-left:44px"></textarea>
                <svg viewBox="0 0 24 24"><path d="M3 5v14h18V5H3zm16 12H5V7h14v10zm-2-2H7v-2h10v2zm0-4H7V9h10v2z"/></svg>
            </div>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">Confirm Booking</button>
    </form>

    <div class="footer-info">
        <p>
            <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
            Your information is secure and encrypted
        </p>
    </div>

</div>

<script>
// ── DOB → Age auto-calculation ──
document.getElementById('dob').addEventListener('change', function () {
    const dob = new Date(this.value);
    if (!this.value || isNaN(dob)) return;
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    document.getElementById('age').value = age;
});

// ── PRE-FILL: look up patient when phone loses focus ──
const phoneInput = document.getElementById('patient_phone');

phoneInput.addEventListener('blur', function () {
    const phone = this.value.trim();
    if (!phone) return;

    fetch(`/booking/lookup-patient?phone=${encodeURIComponent(phone)}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.found) return;
        const p = data.patient;
        const fill = (id, val) => { if (val && document.getElementById(id)) document.getElementById(id).value = val; };
        const fillSelect = (id, val) => {
            const el = document.getElementById(id);
            if (!el || !val) return;
            [...el.options].forEach(o => { if (o.value === val) o.selected = true; });
        };

        fill('patient_name', p.name);
        fill('ic_passport_no', p.ic_passport_no);
        fill('age', p.age);
        fill('dob', p.dob);
        fillSelect('gender', p.gender);
        fillSelect('blood_type', p.blood_type);
        fillSelect('marital_status', p.marital_status);
        fill('nationality', p.nationality);
        fill('emergency_contact_name', p.emergency_contact_name);
        fill('emergency_contact_no', p.emergency_contact_no);
        fill('address', p.address);
        fill('city', p.city);
        fill('state', p.state);
        fill('postcode', p.postcode);
        fill('country', p.country);

        document.getElementById('prefillBanner').classList.add('show');
    })
    .catch(() => {});
});

// ── FORM SUBMIT ──
document.getElementById('bookingForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.classList.add('loading');
    btn.textContent = 'Booking...';

    fetch("{{ route('booking.ajax.store', $doctorId) }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(data => {
        btn.classList.remove('loading');
        if (data.success) {
            this.innerHTML = `
                <div style="text-align:center;padding:30px">
                    <h2 style="color:#4CAF50">✅ Booking Confirmed!</h2>
                    <p style="margin:10px 0">Your booking code:</p>
                    <h1 style="letter-spacing:3px;color:#1363C6">${data.booking_code}</h1>
                    <p>Please keep this code to check your status.</p>
                    <a href="/booking/status/${data.booking_code}"
                       style="display:inline-block;margin-top:20px;background:#1363C6;
                              color:#fff;padding:12px 25px;border-radius:10px;
                              text-decoration:none;font-weight:600">
                        Check Booking Status
                    </a>
                </div>`;
        } else {
            btn.textContent = 'Confirm Booking';
            alert('Something went wrong. Please try again.');
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