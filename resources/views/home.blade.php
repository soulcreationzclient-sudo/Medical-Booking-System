@extends('layouts.app1')

@section('content')
@php
    $doctor = auth()->user();
    $doctorName = $doctor->name ?? 'Doctor';
@endphp

<div class="container">
    {{-- Welcome banner --}}
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <div class="dashboard-welcome-banner shadow-lg">
                <div class="welcome-banner-left">
                    <div class="welcome-banner-datetime" id="welcome-banner-datetime">
                        {{ now()->format('M d, Y h:i A') }}
                    </div>
                    <h2 class="welcome-banner-title">
                        Good Day,
                        @if ($doctor && $doctor->role === 'doctor')
                            Dr. {{ $doctorName }}!
                        @elseif ($doctor && $doctor->role === 'hospital')
                            {{ $doctorName }}!
                        @elseif ($doctor && $doctor->role === 'super_admin')
                            Admin {{ $doctorName }}!
                        @else
                            {{ $doctorName }}!
                        @endif
                    </h2>
                    <p class="welcome-banner-subtitle">
                        Have a Nice <span id="welcome-banner-weekday">{{ now()->format('l') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function updateWelcomeBannerDateTime() {
            const now = new Date();

            const dateTimeElement = document.getElementById('welcome-banner-datetime');
            const weekdayElement = document.getElementById('welcome-banner-weekday');

            if (dateTimeElement) {
                const options = {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                dateTimeElement.textContent = now.toLocaleString(undefined, options);
            }

            if (weekdayElement) {
                const weekday = now.toLocaleDateString(undefined, { weekday: 'long' });
                weekdayElement.textContent = weekday;
            }
        }

        updateWelcomeBannerDateTime();
        setInterval(updateWelcomeBannerDateTime, 60000);
    });
</script>
@endsection
