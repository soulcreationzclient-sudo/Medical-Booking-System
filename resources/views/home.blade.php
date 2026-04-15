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

    {{-- ── API Token Card ── --}}
    @if(auth()->user()->role === 'hospital_admin')
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <div class="p-3 rounded-3" style="background:#f0f6ff;border:1px solid #b5d4f4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span style="font-size:13px;font-weight:600;color:#0C447C">Your API Token</span>
                    <span style="font-size:11px;background:#E6F1FB;color:#185FA5;padding:1px 8px;border-radius:999px;font-weight:500">Swagger / cURL</span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <input type="password" id="apiTokenInput" class="form-control form-control-sm" readonly
                           value="{{ auth()->user()->api_code }}"
                           style="font-family:monospace;font-size:13px;max-width:360px;background:#fff;border-color:#b5d4f4">
                    <button class="btn btn-sm" onclick="toggleApiToken()" id="toggleBtn"
                            style="background:#E6F1FB;color:#0C447C;border:1px solid #b5d4f4;font-size:12px">Show</button>
                    <button class="btn btn-sm" onclick="copyApiToken()" id="copyTokenBtn"
                            style="background:#1363C6;color:#fff;font-size:12px">Copy</button>
                    <a href="/api/documentation" target="_blank" class="btn btn-sm"
                       style="background:#0F6E56;color:#fff;font-size:12px">Open Swagger UI</a>
                </div>
                <small class="text-muted mt-1 d-block" style="font-size:11px">
                    Use as <code>Authorization: Bearer {token}</code> in all API requests.
                </small>
            </div>
        </div>
    </div>
    @endif

        {{-- ── AI Search Card ── --}}
    @if(auth()->user()->role === 'hospital_admin')
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <div class="p-3 rounded-3" style="background:#f8f9ff;border:1px solid #d7defa">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span style="font-size:16px;font-weight:600;color:#2c3e50">
                                AI Search
                            </span>
                            <span style="font-size:11px;background:#e9edff;color:#4154b3;padding:2px 8px;border-radius:999px;font-weight:500">
                                Natural Language Search
                            </span>
                        </div>

                        <p class="mb-0 text-muted" style="font-size:13px">
                            Seach queries in your
                            <strong>natural language</strong>.
                        </p>
                    </div>

                    <div>
                        <a href="{{ route('hospital_admin.ai_search.index') }}" class="btn btn-primary">
                            Open AI Search
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
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

    function toggleApiToken() {
        const input = document.getElementById('apiTokenInput');
        const btn   = document.getElementById('toggleBtn');
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = 'Hide';
        } else {
            input.type = 'password';
            btn.textContent = 'Show';
        }
    }

    function copyApiToken() {
        const input = document.getElementById('apiTokenInput');
        if (!input) return;
        const prevType = input.type;
        input.type = 'text';
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            input.type = prevType;
            const btn = document.getElementById('copyTokenBtn');
            btn.textContent = 'Copied!';
            btn.style.background = '#198754';
            setTimeout(() => {
                btn.textContent = 'Copy';
                btn.style.background = '#1363C6';
            }, 2000);
        });
    }
</script>
@endsection