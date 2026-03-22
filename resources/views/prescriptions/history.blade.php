@extends('layouts.app1')
@section('title', 'Prescription History')
@section('content')

<style>
    .hist-hero {
        background: linear-gradient(135deg, #1363C6 0%, #0a3d8f 100%);
        padding: 32px 0 60px; margin-bottom: -36px;
    }
    .back-btn {
        display: inline-flex; align-items: center; gap: 6px;
        color: rgba(255,255,255,0.8); text-decoration: none;
        font-size: 14px; font-weight: 500; margin-bottom: 18px;
    }
    .back-btn:hover { color: #fff; }

    .hist-card {
        background: #fff; border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        margin-bottom: 20px; overflow: hidden;
    }
    .hist-card-header {
        padding: 16px 22px; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .visit-date {
        display: flex; align-items: center; gap: 14px;
    }
    .date-box {
        background: #1363C6; color: #fff;
        border-radius: 10px; padding: 8px 14px;
        text-align: center; min-width: 54px;
    }
    .date-day   { font-size: 20px; font-weight: 700; line-height: 1; }
    .date-month { font-size: 10px; font-weight: 600; opacity: 0.85; margin-top: 2px; }
    .visit-info-title { font-size: 14px; font-weight: 700; color: #1e293b; }
    .visit-info-sub   { font-size: 12px; color: #94a3b8; margin-top: 2px; }

    table.hist-table { width: 100%; border-collapse: collapse; }
    table.hist-table th {
        padding: 10px 18px; background: #f8fafc;
        text-align: left; font-size: 11px;
        font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.04em;
        border-bottom: 2px solid #e2e8f0;
    }
    table.hist-table td {
        padding: 12px 18px; font-size: 13px; color: #334155;
        border-bottom: 1px solid #f8fafc; vertical-align: top;
    }
    table.hist-table tbody tr:hover { background: #fafbff; }

    .rx-badge {
        display: inline-flex; align-items: center; justify-content: center;
        width: 24px; height: 24px;
        background: #eff6ff; color: #1363C6;
        border-radius: 50%; font-size: 11px; font-weight: 700;
    }

    .empty-hist {
        text-align: center; padding: 60px 20px; color: #94a3b8;
    }

    .btn-view-rx {
        background: #eff6ff; color: #1363C6; border: none;
        padding: 5px 12px; border-radius: 7px;
        font-size: 12px; font-weight: 600;
        text-decoration: none; display: inline-block;
        transition: all 0.2s;
    }
    .btn-view-rx:hover { background: #dbeafe; color: #1363C6; }
</style>

<div class="hist-hero">
    <div class="container">
        <a href="{{ route('hospital_admin.patients.profile', $patient->id) }}" class="back-btn">
            ← Back to Patient Profile
        </a>
        <div style="color:#fff">
            <div style="font-size:13px;opacity:0.7;margin-bottom:4px">Prescription History</div>
            <div style="font-size:24px;font-weight:700">{{ $patient->name }}</div>
            <div style="font-size:14px;opacity:0.75;margin-top:4px">{{ $patient->phone_no }}</div>
        </div>
    </div>
</div>

<div class="container pb-5">

    @if($prescriptions->count() > 0)
        {{-- Group by booking_date + booking --}}
        @php
            $grouped = $prescriptions->groupBy(function($rx) {
                return $rx->booking_date . '||' . $rx->booking_id ?? 'unknown';
            });
        @endphp

        @foreach($grouped as $key => $group)
            @php
                $first = $group->first();
                [$date, $bookingId] = explode('||', $key);
            @endphp
            <div class="hist-card">
                <div class="hist-card-header">
                    <div class="visit-date">
                        <div class="date-box">
                            <div class="date-day">{{ \Carbon\Carbon::parse($date)->format('d') }}</div>
                            <div class="date-month">{{ \Carbon\Carbon::parse($date)->format('M Y') }}</div>
                        </div>
                        <div>
                            <div class="visit-info-title">
                                Dr. {{ $first->doctor_name ?? 'Unknown' }}
                            </div>
                            <div class="visit-info-sub">
                                {{ \Carbon\Carbon::parse($first->start_time)->format('h:i A') }}
                                @if($first->cause) · {{ Str::limit($first->cause, 40) }} @endif
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px">
                        <span style="background:#eff6ff;color:#1363C6;padding:3px 10px;
                                     border-radius:20px;font-size:12px;font-weight:700">
                            {{ $group->count() }} meds
                        </span>
                        @if($bookingId !== 'unknown')
                            <a href="{{ route('prescriptions.print', $bookingId) }}"
                               target="_blank" class="btn-view-rx">
                                🖨️ Print
                            </a>
                            <a href="{{ route('prescriptions.show', $bookingId) }}"
                               class="btn-view-rx">
                                View →
                            </a>
                        @endif
                    </div>
                </div>

                <table class="hist-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Medicine</th>
                            <th>Dosage</th>
                            <th>Frequency</th>
                            <th>Duration</th>
                            <th>Instructions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group as $i => $rx)
                            <tr>
                                <td><span class="rx-badge">{{ $i + 1 }}</span></td>
                                <td style="font-weight:600">{{ $rx->medicine_name }}</td>
                                <td>{{ $rx->dosage ?? '—' }}</td>
                                <td>{{ $rx->frequency ?? '—' }}</td>
                                <td>{{ $rx->duration ?? '—' }}</td>
                                <td style="color:#64748b">{{ $rx->instructions ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

    @else
        <div class="hist-card">
            <div class="empty-hist">
                <div style="font-size:40px;margin-bottom:12px">💊</div>
                <div style="font-size:16px;font-weight:600;color:#64748b;margin-bottom:6px">
                    No prescription history
                </div>
                <div style="font-size:13px">This patient has no recorded prescriptions yet</div>
            </div>
        </div>
    @endif

</div>

@endsection