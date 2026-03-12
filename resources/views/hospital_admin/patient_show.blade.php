@extends('layouts.app1')
@section('title', 'Patient Profile')
@section('content')

<style>
    .profile-hero {
        background: linear-gradient(135deg, #1363C6 0%, #0a3d8f 100%);
        padding: 36px 0 70px;
        margin-bottom: -40px;
    }

    .back-btn {
        display: inline-flex; align-items: center; gap: 6px;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        font-size: 14px; font-weight: 500;
        margin-bottom: 20px;
        transition: color 0.2s;
    }
    .back-btn:hover { color: #fff; }

    .profile-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 40px rgba(19,99,198,0.13);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .profile-top {
        padding: 28px 30px;
        display: flex; align-items: center; gap: 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .profile-avatar {
        width: 72px; height: 72px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1363C6, #4a90e2);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 4px 16px rgba(19,99,198,0.3);
    }

    .profile-name { font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .profile-sub  { font-size: 14px; color: #64748b; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0;
    }
    .info-item {
        padding: 20px 24px;
        border-right: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }
    .info-item:nth-child(3n) { border-right: none; }
    .info-label {
        font-size: 11px; font-weight: 700;
        color: #94a3b8; text-transform: uppercase;
        letter-spacing: 0.05em; margin-bottom: 5px;
    }
    .info-value { font-size: 14px; font-weight: 600; color: #1e293b; }
    .info-value.empty { color: #cbd5e1; font-weight: 400; }

    /* SECTION TITLES */
    .section-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .section-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .section-header h6 {
        font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;
        display: flex; align-items: center; gap: 8px;
    }
    .section-icon {
        width: 30px; height: 30px;
        background: #eff6ff; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
    }
    .section-icon svg { width: 16px; height: 16px; fill: #1363C6; }

    /* BOOKINGS TABLE */
    table.bookings-table { width: 100%; border-collapse: collapse; }
    table.bookings-table thead tr {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    table.bookings-table th {
        padding: 12px 20px;
        text-align: left; font-size: 12px;
        font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.04em;
    }
    table.bookings-table tbody tr { border-bottom: 1px solid #f1f5f9; }
    table.bookings-table tbody tr:last-child { border-bottom: none; }
    table.bookings-table td {
        padding: 14px 20px;
        font-size: 14px; color: #334155; vertical-align: middle;
    }

    .status-badge {
        padding: 4px 11px; border-radius: 20px;
        font-size: 12px; font-weight: 600; display: inline-block;
    }
    .status-pending  { background: #fef9c3; color: #854d0e; }
    .status-accepted { background: #dcfce7; color: #166534; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    .status-completed{ background: #eff6ff; color: #1d4ed8; }

    .empty-bookings {
        text-align: center; padding: 40px 20px; color: #94a3b8;
    }
    .empty-bookings svg { width: 40px; height: 40px; fill: #cbd5e1; margin-bottom: 12px; }

    @media (max-width: 768px) {
        .info-grid { grid-template-columns: repeat(2, 1fr); }
        .info-item:nth-child(3n) { border-right: 1px solid #f1f5f9; }
        .info-item:nth-child(2n) { border-right: none; }
        .profile-top { flex-direction: column; text-align: center; }
    }
    @media (max-width: 480px) {
        .info-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="profile-hero">
    <div class="container">
        <a href="{{ route('hospital_admin.patients.search') }}" class="back-btn">
            ← Back to Search
        </a>
        <div style="color:#fff">
            <div style="font-size:13px;opacity:0.7;margin-bottom:4px">Patient Profile</div>
            <div style="font-size:26px;font-weight:700">{{ $patient->name }}</div>
        </div>
    </div>
</div>

<div class="container pb-5">

    {{-- ── PROFILE CARD ── --}}
    <div class="profile-card">

        <div class="profile-top">
            <div class="profile-avatar">{{ strtoupper(substr($patient->name, 0, 1)) }}</div>
            <div>
                <div class="profile-name">{{ $patient->name }}</div>
                <div class="profile-sub">
                    {{ $patient->phone_no }}
                    @if(!empty($patient->email)) · {{ $patient->email }} @endif
                </div>
            </div>
        </div>

        {{-- Personal Details --}}
        <div style="padding:16px 24px 4px;border-bottom:1px solid #f1f5f9">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em">
                Personal Information
            </div>
        </div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">IC / Passport No</div>
                <div class="info-value {{ $patient->ic_passport_no ? '' : 'empty' }}">
                    {{ $patient->ic_passport_no ?? 'Not provided' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Date of Birth</div>
                <div class="info-value {{ $patient->dob ? '' : 'empty' }}">
                    {{ $patient->dob ? \Carbon\Carbon::parse($patient->dob)->format('d M Y') : 'Not provided' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Age</div>
                <div class="info-value {{ $patient->age ? '' : 'empty' }}">
                    {{ $patient->age ? $patient->age . ' years' : 'Not provided' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Gender</div>
                <div class="info-value {{ $patient->gender ? '' : 'empty' }}">
                    {{ $patient->gender ? ucfirst($patient->gender) : 'Not provided' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Blood Type</div>
                <div class="info-value {{ $patient->blood_type ? '' : 'empty' }}">
                    {{ $patient->blood_type ?? 'Not provided' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Marital Status</div>
                <div class="info-value {{ $patient->marital_status ? '' : 'empty' }}">
                    {{ $patient->marital_status ? ucfirst($patient->marital_status) : 'Not provided' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Nationality</div>
                <div class="info-value {{ $patient->nationality ? '' : 'empty' }}">
                    {{ $patient->nationality ?? 'Not provided' }}
                </div>
            </div>
        </div>

        {{-- Address --}}
        <div style="padding:16px 24px 4px;border-bottom:1px solid #f1f5f9;border-top:1px solid #f1f5f9">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em">
                Address
            </div>
        </div>
        <div class="info-grid">
            <div class="info-item" style="grid-column: 1 / -1">
                <div class="info-label">Street Address</div>
                <div class="info-value {{ $patient->address ? '' : 'empty' }}">
                    {{ $patient->address ?? 'Not provided' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">City</div>
                <div class="info-value {{ $patient->city ? '' : 'empty' }}">{{ $patient->city ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">State</div>
                <div class="info-value {{ $patient->state ? '' : 'empty' }}">{{ $patient->state ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Postcode</div>
                <div class="info-value {{ $patient->postcode ? '' : 'empty' }}">{{ $patient->postcode ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Country</div>
                <div class="info-value {{ $patient->country ? '' : 'empty' }}">{{ $patient->country ?? '—' }}</div>
            </div>
        </div>

        {{-- Emergency Contact --}}
        <div style="padding:16px 24px 4px;border-bottom:1px solid #f1f5f9;border-top:1px solid #f1f5f9">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em">
                Emergency Contact
            </div>
        </div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Name</div>
                <div class="info-value {{ $patient->emergency_contact_name ? '' : 'empty' }}">
                    {{ $patient->emergency_contact_name ?? 'Not provided' }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Phone</div>
                <div class="info-value {{ $patient->emergency_contact_no ? '' : 'empty' }}">
                    {{ $patient->emergency_contact_no ?? 'Not provided' }}
                </div>
            </div>
        </div>

    </div>

    {{-- ── BOOKING HISTORY ── --}}
    <div class="section-card">
        <div class="section-header">
            <h6>
                <div class="section-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                </div>
                Booking History
            </h6>
            <span style="background:#eff6ff;color:#1363C6;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">
                {{ $bookings->count() }} total
            </span>
        </div>

        @if($bookings->count() > 0)
            <div style="overflow-x:auto">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Doctor</th>
                            <th>Cause</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <span style="font-family:monospace;font-weight:700;color:#1363C6;font-size:13px">
                                        {{ $booking->action_token }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}</td>
                                <td>
                                    <span style="font-weight:600">{{ $booking->doctor_name ?? '—' }}</span>
                                </td>
                                <td style="max-width:180px">
                                    <span style="color:#64748b">
                                        {{ $booking->cause ? Str::limit($booking->cause, 40) : '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $booking->status }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-bookings">
                <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                <div style="font-size:15px;font-weight:600;color:#64748b;margin-bottom:4px">No bookings yet</div>
                <div style="font-size:13px">This patient has no booking history</div>
            </div>
        @endif
    </div>

</div>

@endsection