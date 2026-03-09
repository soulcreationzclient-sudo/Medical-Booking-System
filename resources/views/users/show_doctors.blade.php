<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Doctors</title>

    <!-- Open Sans Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Open Sans", sans-serif;
            background: #ffff;
            min-height: 100vh;
            padding: 30px 16px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            color: #1363C6;
        }

        .page-header h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .page-header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .doctor-card {
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            transition: transform 0.25s ease;
        }

        .doctor-card:hover {
            transform: translateY(-5px);
        }

        .doctor-image {
            width: 100%;
            height: 210px;
            background: #f1f3f5;
            overflow: hidden;
        }

        .doctor-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .doctor-info {
            padding: 18px;
        }

        .doctor-name {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 6px;
        }

        .specialization {
            display: inline-block;
            background: #1363C6;
            color: white;
            padding: 4px 12px;
            border-radius: 18px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .qualification {
            color: #718096;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .experience-years {
            display: inline-block;
            background: #e6f0ff;
            color: #1363C6;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .description {
            color: #4a5568;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .booking-form {
            margin-top: 10px;
        }

        .date-input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px dashed #bbb;
            font-size: 14px;
            background: #f8f9fa;
            cursor: pointer;
            margin-bottom: 12px;
        }

        .hidden {
            display: none;
        }

        .book-btn {
            width: 100%;
            padding: 12px;
            background: #1363C6;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .book-btn:hover {
            opacity: 0.95;
        }

        /* Mobile */
        @media (max-width: 768px) {
            body {
                padding: 20px 12px;
            }

            .page-header h1 {
                font-size: 28px;
            }

            .doctors-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .doctor-image {
                height: 190px;
            }
        }

        /* Error Toast */
        .danger-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 260px;
            max-width: 360px;
            padding: 12px 16px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            z-index: 1080;
            animation: danger-toast-in 0.4s ease forwards;
        }

        @keyframes danger-toast-in {
            from {
                opacity: 0;
                transform: translateX(25px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>

<body>
    <div class="container">

        @if (session('error'))
            <div class="danger-toast">
                <strong>❌ Error</strong> Doctors not available on this day
            </div>
        @endif

        <header class="page-header">
            <h1>Our Doctors</h1>
            <p>Choose your doctor and appointment date</p>
        </header>

        <div class="doctors-grid">
            @foreach ($doctor as $doc)
                <div class="doctor-card">

                    <div class="doctor-image">
                        <img src="{{ asset('storage/' . $doc->profile_photo) }}" alt="{{ $doc->name }}">
                    </div>

                    <div class="doctor-info">
                        <h3 class="doctor-name">{{ $doc->name }}</h3>

                        <span class="specialization">{{ $doc->specialization }}</span>

                        <p class="qualification">{{ $doc->qualification }}</p>

                        <span class="experience-years">
                            {{ $doc->experience_years }} Years Experience
                        </span>

                        <p class="description">
                            {{ $doc->description }}
                        </p>
                        <?php
                        $phone_no=$_GET['phone_no']??null;
                        ?>
                        <!-- BOOKING FORM -->
                        <form method="GET" action="{{ route('patient.booking', $doc->id) }}" class="booking-form">
                            @php
                                $today = now();
                                $nextWeek = now()->addDays(7);
                            @endphp

                            <input type="date" name="date" class="date-input hidden"
                                     min="{{ $today->toDateString() }}"
       max="{{ $nextWeek->toDateString() }}"
       value="{{ $today->toDateString() }}"  required>
        <input type="hidden" name="phone_no" value="{{ $phone_no }}">

                            <button type="button" class="book-btn" onclick="handleBookingClick(this)">
                                Book Appointment
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function handleBookingClick(button) {
            const form = button.closest('form');
            const dateInput = form.querySelector('.date-input');

            if (dateInput.classList.contains('hidden')) {
                dateInput.classList.remove('hidden');
                dateInput.focus();
                button.innerText = 'Confirm Date';
                return;
            }

            form.submit();
        }

        setTimeout(() => {
            const toast = document.querySelector('.danger-toast');
            if (toast) toast.remove();
        }, 4000);
    </script>
</body>

</html>
