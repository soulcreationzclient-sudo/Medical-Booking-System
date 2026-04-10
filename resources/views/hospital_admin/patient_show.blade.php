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
        text-decoration: none; font-size: 14px; font-weight: 500;
        margin-bottom: 20px; transition: color 0.2s;
    }
    .back-btn:hover { color: #fff; }

    .profile-card, .section-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .profile-card { border-radius: 20px; box-shadow: 0 8px 40px rgba(19,99,198,0.13); }

    .profile-top {
        padding: 28px 30px;
        display: flex; align-items: center; gap: 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .profile-avatar {
        width: 72px; height: 72px; border-radius: 50%;
        background: linear-gradient(135deg, #1363C6, #4a90e2);
        color: #fff; display: flex; align-items: center; justify-content: center;
        font-size: 28px; font-weight: 700; flex-shrink: 0;
        box-shadow: 0 4px 16px rgba(19,99,198,0.3);
    }
    .profile-name { font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .profile-sub  { font-size: 14px; color: #64748b; }

    .info-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 0;
    }
    .info-item {
        padding: 20px 24px;
        border-right: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }
    .info-item:nth-child(3n) { border-right: none; }
    .info-label {
        font-size: 11px; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 5px;
    }
    .info-value { font-size: 14px; font-weight: 600; color: #1e293b; }
    .info-value.empty { color: #cbd5e1; font-weight: 400; }

    .edit-input, .edit-select, .edit-textarea {
        width: 100%; padding: 8px 10px;
        border: 1.5px solid #1363C6;
        border-radius: 8px; font-size: 14px;
        color: #1e293b; background: #f0f7ff;
        outline: none; transition: box-shadow 0.2s;
        font-family: inherit;
    }
    .edit-input:focus, .edit-select:focus, .edit-textarea:focus {
        box-shadow: 0 0 0 3px rgba(19,99,198,0.12);
    }
    .edit-textarea { resize: vertical; min-height: 70px; }

    .view-mode  { display: block; }
    .edit-mode  { display: none; }
    body.editing .view-mode { display: none; }
    body.editing .edit-mode  { display: block; }

    .btn-edit {
        padding: 8px 20px; background: #1363C6; color: #fff;
        border: none; border-radius: 10px; font-size: 14px;
        font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 7px; transition: all 0.2s;
    }
    .btn-edit:hover { background: #0f52a8; }
    .btn-save {
        padding: 8px 20px; background: #16a34a; color: #fff;
        border: none; border-radius: 10px; font-size: 14px;
        font-weight: 600; cursor: pointer;
        display: none; align-items: center; gap: 7px; transition: all 0.2s;
    }
    .btn-save:hover { background: #15803d; }
    .btn-cancel {
        padding: 8px 16px; background: #f1f5f9; color: #64748b;
        border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 14px;
        font-weight: 600; cursor: pointer;
        display: none; align-items: center; gap: 7px; transition: all 0.2s;
    }
    .btn-cancel:hover { background: #e2e8f0; }
    body.editing .btn-edit   { display: none; }
    body.editing .btn-save   { display: inline-flex; }
    body.editing .btn-cancel { display: inline-flex; }

    .section-header {
        padding: 18px 24px; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .section-header h6 {
        font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;
        display: flex; align-items: center; gap: 8px;
    }
    .section-icon {
        width: 30px; height: 30px; background: #eff6ff;
        border-radius: 8px; display: flex; align-items: center; justify-content: center;
    }
    .section-icon svg { width: 16px; height: 16px; fill: #1363C6; }

    .section-divider {
        padding: 16px 24px 4px;
        border-bottom: 1px solid #f1f5f9;
        border-top: 1px solid #f1f5f9;
    }
    .section-divider-label {
        font-size: 11px; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.05em;
    }

    table.bookings-table { width: 100%; border-collapse: collapse; }
    table.bookings-table thead tr { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
    table.bookings-table th {
        padding: 12px 20px; text-align: left; font-size: 12px;
        font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;
    }
    table.bookings-table tbody tr { border-bottom: 1px solid #f1f5f9; }
    table.bookings-table tbody tr:last-child { border-bottom: none; }
    table.bookings-table td { padding: 14px 20px; font-size: 14px; color: #334155; vertical-align: middle; }

    .status-badge { padding: 4px 11px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
    .status-pending   { background: #fef9c3; color: #854d0e; }
    .status-accepted  { background: #dcfce7; color: #166534; }
    .status-rejected  { background: #fee2e2; color: #991b1b; }
    .status-completed { background: #eff6ff; color: #1d4ed8; }
    .status-rescheduled { background: #ede9fe; color: #5b21b6; }
    .status-no_show   { background: #f3f4f6; color: #4b5563; }
    .status-cancelled { background: #f3f4f6; color: #374151; }

    /* ── RX STYLES ── */
    .rx-card {
        border: 1.5px solid #e2e8f0; border-radius: 12px;
        padding: 16px 20px; margin-bottom: 12px;
        transition: border-color 0.2s;
    }
    .rx-card:hover { border-color: #1363C6; }
    .rx-booking-tag {
        display: inline-block; font-size: 11px; font-weight: 600;
        background: #eff6ff; color: #1363C6;
        padding: 2px 8px; border-radius: 20px; margin-bottom: 8px;
    }
    .rx-medicine-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px;
    }
    .rx-medicine-row:last-child { border-bottom: none; }
    .rx-notes { font-size: 13px; color: #94a3b8; margin-top: 8px; font-style: italic; }

    .btn-toggle-rx {
        padding: 7px 16px; background: #eff6ff; color: #1363C6;
        border: 1.5px solid #bfdbfe; border-radius: 8px; font-size: 13px;
        font-weight: 600; cursor: pointer; transition: all 0.2s;
        text-decoration: none; display: inline-block;
    }
    .btn-toggle-rx:hover { background: #dbeafe; color: #1363C6; }

    .btn-add-rx {
        padding: 8px 18px; background: #1363C6; color: #fff;
        border: none; border-radius: 8px; font-size: 13px;
        font-weight: 600; cursor: pointer; transition: background 0.2s;
    }
    .btn-add-rx:hover { background: #0f52a8; }

    .empty-state {
        text-align: center; padding: 40px 20px; color: #94a3b8;
    }
    .empty-state svg {
        width: 40px; height: 40px; fill: #cbd5e1; margin-bottom: 12px;
        display: block; margin-left: auto; margin-right: auto;
    }

    .alert-success {
        background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;
        border-radius: 10px; padding: 12px 18px; margin-bottom: 20px;
        font-size: 14px; font-weight: 600;
    }

    /* ── BILLING STYLES ── */
    .billing-summary {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .billing-summary-item {
        padding: 20px 24px;
        border-right: 1px solid #f1f5f9;
    }
    .billing-summary-item:last-child { border-right: none; }
    .billing-summary-label {
        font-size: 11px; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;
    }
    .billing-summary-value { font-size: 22px; font-weight: 800; }

    .billing-table { width: 100%; border-collapse: collapse; }
    .billing-table thead tr { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
    .billing-table th {
        padding: 11px 18px; text-align: left; font-size: 11px;
        font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .billing-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
    .billing-table tbody tr:last-child { border-bottom: none; }
    .billing-table tbody tr:hover { background: #f8fafc; }
    .billing-table td { padding: 13px 18px; font-size: 14px; color: #334155; vertical-align: middle; }

    .type-badge {
        padding: 3px 10px; border-radius: 20px; font-size: 11px;
        font-weight: 700; display: inline-block; white-space: nowrap;
    }
    .type-consultation   { background: #eff6ff; color: #1d4ed8; }
    .type-medicine       { background: #f0fdf4; color: #166534; }
    .type-treatment      { background: #fefce8; color: #854d0e; }
    .type-operation      { background: #fee2e2; color: #991b1b; }
    .type-custom_profit  { background: #f0fdf4; color: #166534; }
    .type-custom_expense { background: #f1f5f9; color: #475569; }

    .paid-badge   { background: #dcfce7; color: #166534; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .unpaid-badge { background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .note-badge   { background: #f1f5f9; color: #475569; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }

    .btn-pay {
        padding: 5px 12px; background: #16a34a; color: #fff;
        border: none; border-radius: 7px; font-size: 12px;
        font-weight: 600; cursor: pointer; transition: background 0.2s;
    }
    .btn-pay:hover { background: #15803d; }
    .btn-del-entry {
        padding: 5px 10px; background: #fee2e2; color: #991b1b;
        border: none; border-radius: 7px; font-size: 12px;
        font-weight: 600; cursor: pointer; transition: background 0.2s; margin-left: 4px;
    }
    .btn-del-entry:hover { background: #fecaca; }

    .billing-form { padding: 20px 24px; border-top: 1px solid #f1f5f9; display: none; }
    .billing-form.open { display: block; }
    .billing-grid {
        display: grid; grid-template-columns: 1fr 1fr 1fr;
        gap: 14px; margin-bottom: 14px;
    }
    .billing-field label {
        display: block; font-size: 11px; font-weight: 700;
        color: #94a3b8; text-transform: uppercase;
        letter-spacing: 0.05em; margin-bottom: 5px;
    }
    .billing-field input, .billing-field select {
        width: 100%; padding: 9px 12px;
        border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 14px; color: #1e293b; outline: none;
        transition: border-color 0.2s; font-family: inherit; background: #fff;
    }
    .billing-field input:focus, .billing-field select:focus {
        border-color: #1363C6; box-shadow: 0 0 0 3px rgba(19,99,198,0.08);
    }

    .treatment-form { padding: 20px 24px; border-top: 1px solid #f1f5f9; display: none; }
    .treatment-form.open { display: block; }

    .filter-tabs { display: flex; gap: 8px; padding: 14px 24px; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; }
    .filter-tab {
        padding: 5px 14px; border-radius: 20px; font-size: 12px;
        font-weight: 600; cursor: pointer; border: 1.5px solid #e2e8f0;
        background: #fff; color: #64748b; transition: all 0.2s;
    }
    .filter-tab.active, .filter-tab:hover { background: #1363C6; color: #fff; border-color: #1363C6; }

    /* prescription booking buttons */
    .rx-booking-btn {
        display: block; width: 100%; padding: 10px 16px; margin-bottom: 8px;
        background: #eff6ff; color: #1363C6; border: 1.5px solid #bfdbfe;
        border-radius: 8px; font-size: 13px; font-weight: 600;
        text-decoration: none; text-align: center; transition: all 0.2s;
    }
    .rx-booking-btn:hover { background: #1363C6; color: #fff; border-color: #1363C6; }
    .rx-booking-btn.completed { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
    .rx-booking-btn.accepted  { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }

    @media (max-width: 768px) {
        .info-grid, .billing-summary { grid-template-columns: repeat(2, 1fr); }
        .billing-grid { grid-template-columns: 1fr 1fr; }
        .profile-top { flex-direction: column; text-align: center; }
    }
    @media (max-width: 480px) {
        .info-grid, .billing-summary { grid-template-columns: 1fr; }
        .billing-grid { grid-template-columns: 1fr; }
    }
</style>

{{-- ── HERO ── --}}
<div class="profile-hero">
    <div class="container">
        <a href="{{ route('hospital_admin.patients.search') }}" class="back-btn">← Back to Search</a>
        <div style="color:#fff">
            <div style="font-size:13px;opacity:0.7;margin-bottom:4px">Patient Profile</div>
            <div style="font-size:26px;font-weight:700">{{ $patient->name }}</div>
        </div>
    </div>
</div>

<div class="container pb-5">

    @if(session('success'))
        <div class="alert-success">✓ {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;
                    border-radius:10px;padding:12px 18px;margin-bottom:20px;font-size:14px;">
            @foreach($errors->all() as $error)<div>✕ {{ $error }}</div>@endforeach
        </div>
    @endif

    {{-- Edit/Save/Cancel bar --}}
    <div style="display:flex;gap:10px;justify-content:flex-end;margin-bottom:16px">
        <button class="btn-edit" onclick="enableEdit()">
            <svg style="width:15px;height:15px;fill:white" viewBox="0 0 24 24">
                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm17.71-10.21a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
            </svg>
            Edit Patient
        </button>
        <button class="btn-save" onclick="document.getElementById('editForm').submit()">
            <svg style="width:15px;height:15px;fill:white" viewBox="0 0 24 24">
                <path d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
            </svg>
            Save Changes
        </button>
        <button class="btn-cancel" onclick="disableEdit()">✕ Cancel</button>
    </div>

    {{-- ════════════ PROFILE CARD ════════════ --}}
    <form id="editForm" method="POST" action="{{ route('hospital_admin.patients.update', $patient->id) }}">
        @csrf
        @method('PUT')

        <div class="profile-card">
            <div class="profile-top">
                <div class="profile-avatar">{{ strtoupper(substr($patient->name, 0, 1)) }}</div>
                <div style="flex:1">
                    <div class="view-mode">
                        <div class="profile-name">{{ $patient->name }}</div>
                        <div class="profile-sub">{{ $patient->phone_no }}</div>
                    </div>
                    <div class="edit-mode" style="display:flex;gap:12px;flex-wrap:wrap">
                        <div style="flex:1;min-width:160px">
                            <div class="info-label" style="margin-bottom:5px">Full Name</div>
                            <input type="text" name="name" class="edit-input" value="{{ $patient->name }}" required>
                        </div>
                        <div style="flex:1;min-width:140px">
                            <div class="info-label" style="margin-bottom:5px">Phone Number</div>
                            <input type="text" name="phone_no" class="edit-input" value="{{ $patient->phone_no ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-divider"><div class="section-divider-label">Personal Information</div></div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">IC / Passport No</div>
                    <div class="view-mode info-value {{ ($patient->ic_passport_no ?? null) ? '' : 'empty' }}">{{ $patient->ic_passport_no ?? 'Not provided' }}</div>
                    <div class="edit-mode"><input type="text" name="ic_passport_no" class="edit-input" value="{{ $patient->ic_passport_no ?? '' }}"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date of Birth</div>
                    <div class="view-mode info-value {{ ($patient->dob ?? null) ? '' : 'empty' }}">
                        {{ ($patient->dob ?? null) ? \Carbon\Carbon::parse($patient->dob)->format('d M Y') : 'Not provided' }}
                    </div>
                    <div class="edit-mode">
                        <input type="date" name="dob" class="edit-input"
                               value="{{ ($patient->dob ?? null) ? \Carbon\Carbon::parse($patient->dob)->format('Y-m-d') : '' }}">
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Age</div>
                    <div class="view-mode info-value {{ ($patient->age ?? null) ? '' : 'empty' }}">{{ ($patient->age ?? null) ? $patient->age . ' years' : 'Not provided' }}</div>
                    <div class="edit-mode"><input type="number" name="age" class="edit-input" value="{{ $patient->age ?? '' }}"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Gender</div>
                    <div class="view-mode info-value {{ ($patient->gender ?? null) ? '' : 'empty' }}">{{ ($patient->gender ?? null) ? ucfirst($patient->gender) : 'Not provided' }}</div>
                    <div class="edit-mode">
                        <select name="gender" class="edit-select">
                            <option value="">— Select —</option>
                            <option value="male"   {{ ($patient->gender ?? '') === 'male'   ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ ($patient->gender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other"  {{ ($patient->gender ?? '') === 'other'  ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Blood Type</div>
                    <div class="view-mode info-value {{ ($patient->blood_type ?? null) ? '' : 'empty' }}">{{ $patient->blood_type ?? 'Not provided' }}</div>
                    <div class="edit-mode">
                        <select name="blood_type" class="edit-select">
                            <option value="">— Select —</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                                <option value="{{ $bt }}" {{ ($patient->blood_type ?? '') === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Marital Status</div>
                    <div class="view-mode info-value {{ ($patient->marital_status ?? null) ? '' : 'empty' }}">{{ ($patient->marital_status ?? null) ? ucfirst($patient->marital_status) : 'Not provided' }}</div>
                    <div class="edit-mode">
                        <select name="marital_status" class="edit-select">
                            <option value="">— Select —</option>
                            @foreach(['single','married','divorced','widowed'] as $ms)
                                <option value="{{ $ms }}" {{ strtolower($patient->marital_status ?? '') === $ms ? 'selected' : '' }}>{{ ucfirst($ms) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nationality</div>
                    <div class="view-mode info-value {{ ($patient->nationality ?? null) ? '' : 'empty' }}">{{ $patient->nationality ?? 'Not provided' }}</div>
                    <div class="edit-mode"><input type="text" name="nationality" class="edit-input" value="{{ $patient->nationality ?? '' }}"></div>
                </div>
            </div>

            <div class="section-divider"><div class="section-divider-label">Address</div></div>
            <div class="info-grid">
                <div class="info-item" style="grid-column: 1 / -1">
                    <div class="info-label">Street Address</div>
                    <div class="view-mode info-value {{ ($patient->address ?? null) ? '' : 'empty' }}">{{ $patient->address ?? 'Not provided' }}</div>
                    <div class="edit-mode"><textarea name="address" class="edit-textarea">{{ $patient->address ?? '' }}</textarea></div>
                </div>
                <div class="info-item">
                    <div class="info-label">City</div>
                    <div class="view-mode info-value {{ ($patient->city ?? null) ? '' : 'empty' }}">{{ $patient->city ?? '—' }}</div>
                    <div class="edit-mode"><input type="text" name="city" class="edit-input" value="{{ $patient->city ?? '' }}"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">State</div>
                    <div class="view-mode info-value {{ ($patient->state ?? null) ? '' : 'empty' }}">{{ $patient->state ?? '—' }}</div>
                    <div class="edit-mode"><input type="text" name="state" class="edit-input" value="{{ $patient->state ?? '' }}"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Postcode</div>
                    <div class="view-mode info-value {{ ($patient->postcode ?? null) ? '' : 'empty' }}">{{ $patient->postcode ?? '—' }}</div>
                    <div class="edit-mode"><input type="text" name="postcode" class="edit-input" value="{{ $patient->postcode ?? '' }}"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Country</div>
                    <div class="view-mode info-value {{ ($patient->country ?? null) ? '' : 'empty' }}">{{ $patient->country ?? '—' }}</div>
                    <div class="edit-mode"><input type="text" name="country" class="edit-input" value="{{ $patient->country ?? '' }}"></div>
                </div>
            </div>

            <div class="section-divider"><div class="section-divider-label">Emergency Contact</div></div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Name</div>
                    <div class="view-mode info-value {{ ($patient->emergency_contact_name ?? null) ? '' : 'empty' }}">{{ $patient->emergency_contact_name ?? 'Not provided' }}</div>
                    <div class="edit-mode"><input type="text" name="emergency_contact_name" class="edit-input" value="{{ $patient->emergency_contact_name ?? '' }}"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone</div>
                    <div class="view-mode info-value {{ ($patient->emergency_contact_no ?? null) ? '' : 'empty' }}">{{ $patient->emergency_contact_no ?? 'Not provided' }}</div>
                    <div class="edit-mode"><input type="text" name="emergency_contact_no" class="edit-input" value="{{ $patient->emergency_contact_no ?? '' }}"></div>
                </div>
            </div>
        </div>
    </form>

    {{-- ════════════ BILLING LEDGER ════════════ --}}
    @php
        $billingEntries = $billingEntries ?? collect();
        $totalDue  = $billingEntries->where('is_paid', false)->where('is_past_note', false)->sum('amount');
        $totalPaid = $billingEntries->where('is_paid', true)->sum('amount');
        $totalBill = $billingEntries->where('is_past_note', false)->sum('amount');
    @endphp

    <div class="section-card">
        <div class="section-header">
            <h6>
                <div class="section-icon">
                    <svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                </div>
                💰 Billing Ledger
            </h6>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <button type="button" class="btn-toggle-rx" onclick="toggleBillingForm()"
                        style="background:#eff6ff;color:#1363C6;border-color:#bfdbfe;">
                    + Add Entry
                </button>
                <button type="button" class="btn-toggle-rx" onclick="toggleTreatmentForm()"
                        style="background:#fef9c3;color:#854d0e;border-color:#fde68a;">
                    🏥 Treatment / Operation
                </button>
            </div>
        </div>

        <div class="billing-summary">
            <div class="billing-summary-item">
                <div class="billing-summary-label">Total Billed</div>
                <div class="billing-summary-value" style="color:#1363C6">RM{{ number_format($totalBill, 2) }}</div>
            </div>
            <div class="billing-summary-item">
                <div class="billing-summary-label">Amount Paid</div>
                <div class="billing-summary-value" style="color:#16a34a">RM{{ number_format($totalPaid, 2) }}</div>
            </div>
            <div class="billing-summary-item">
                <div class="billing-summary-label">Amount Due</div>
                <div class="billing-summary-value" style="color:#dc2626">RM{{ number_format($totalDue, 2) }}</div>
            </div>
        </div>

        <div class="filter-tabs">
            <span class="filter-tab active" onclick="filterBilling('all', this)">All</span>
            <span class="filter-tab" onclick="filterBilling('unpaid', this)">Unpaid</span>
            <span class="filter-tab" onclick="filterBilling('paid', this)">Paid</span>
            <span class="filter-tab" onclick="filterBilling('consultation', this)">Consultation</span>
            <span class="filter-tab" onclick="filterBilling('medicine', this)">Medicine</span>
            <span class="filter-tab" onclick="filterBilling('treatment', this)">Treatment</span>
            <span class="filter-tab" onclick="filterBilling('operation', this)">Operation</span>
        </div>

        {{-- Add Billing Entry Form --}}
        <div class="billing-form" id="billingForm">
            <form method="POST" action="{{ route('hospital_admin.patients.billing.store', $patient->id) }}">
                @csrf
                <div class="billing-grid">
                    <div class="billing-field">
                        <label>Entry Type *</label>
                        <select name="type" required>
                            <option value="consultation">Consultation Fee</option>
                            <option value="medicine">Medicine</option>
                            <option value="custom_profit">Custom Income</option>
                            <option value="custom_expense">Custom Expense</option>
                        </select>
                    </div>
                    <div class="billing-field">
                        <label>Description *</label>
                        <input type="text" name="description" placeholder="e.g. Dr. Kumar consultation" required>
                    </div>
                    <div class="billing-field">
                        <label>Amount (RM) *</label>
                        <input type="number" name="amount" step="0.01" min="0" value="0" required>
                    </div>
                </div>
                <input type="hidden" name="is_past_note" value="0">
                <div style="display:flex;gap:10px">
                    <button type="submit" class="btn-add-rx">+ Save Entry</button>
                    <button type="button" class="btn-toggle-rx" onclick="toggleBillingForm()">Cancel</button>
                </div>
            </form>
        </div>

        {{-- Add Treatment / Operation Form --}}
        <div class="treatment-form" id="treatmentForm">
            <form method="POST" action="{{ route('hospital_admin.patients.billing.store', $patient->id) }}">
                @csrf
                <div class="billing-grid">
                    <div class="billing-field">
                        <label>Type *</label>
                        <select name="type" id="treatmentTypeSelect" required>
                            <option value="treatment">Treatment</option>
                            <option value="operation">Operation</option>
                        </select>
                    </div>

                    <div class="billing-field">
                        <label>Booking</label>
                        <select name="booking_id">
                            <option value="">Not linked to booking</option>
                            @foreach(($bookings ?? collect()) as $booking)
                                <option value="{{ $booking->id }}">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}
                                    @if($booking->doctor_name) - Dr. {{ $booking->doctor_name }} @endif
                                    - {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="billing-field">
                        <label>Treatment *</label>
                        <select name="treatment_id" id="treatmentSelect" required onchange="fillTreatmentPrice()">
                            <option value="">Select treatment</option>
                            @foreach(($treatments ?? collect()) as $treatment)
                                <option
                                    value="{{ $treatment->id }}"
                                    data-price="{{ $treatment->base_price }}"
                                    data-category="{{ $treatment->category }}"
                                    data-name="{{ $treatment->name }}">
                                    {{ $treatment->name }} (RM{{ number_format($treatment->base_price, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="billing-field">
                        <label>Quantity *</label>
                        <input type="number" name="quantity" id="treatmentQuantity" min="1" value="1" required oninput="recalculateTreatmentTotal()">
                    </div>

                    <div class="billing-field">
                        <label>Unit Price (RM) *</label>
                        <input type="number" name="unit_price" id="treatmentUnitPrice" step="0.01" min="0" value="0" required oninput="recalculateTreatmentTotal()">
                    </div>
                    
                    <div class="billing-field">
                        <label>Discount (RM)</label>
                        <input type="number" name="discount_amount" id="treatmentDiscount" step="0.01" min="0" value="0" oninput="recalculateTreatmentTotal()">
                    </div>

                    <div class="billing-field" style="grid-column: span 2">
                        <label>Description / Notes *</label>
                        <input type="text" name="description" id="treatmentDescription" placeholder="Describe the treatment or operation..." >
                    </div>

                    <div class="billing-field" style="display:flex;align-items:center;gap:10px;padding-top:20px">
                        <input type="checkbox" id="isPastRecord" style="width:18px;height:18px;cursor:pointer"
                               onchange="toggleTreatmentAmount(this)">
                        <label for="isPastRecord" style="font-size:13px;color:#475569;cursor:pointer;text-transform:none;letter-spacing:0">
                            Past record (note only — no charge)
                        </label>
                        <input type="hidden" name="is_past_note" id="isPastNoteHidden" value="0">
                    </div>

                    <div class="billing-field" id="treatmentAmountWrapper">
                        <label>Total Amount (RM) *</label>
                        <input type="number" name="amount" id="treatmentAmountInput" step="0.01" min="0" value="0" required readonly>
                    </div>

                    <div class="billing-field" style="grid-column: span 3">
                        <label>Internal Notes</label>
                        <input type="text" name="notes" placeholder="Optional booking treatments note">
                    </div>
                </div>
                <div style="display:flex;gap:10px">
                    <button type="submit" class="btn-add-rx" style="background:#d97706">+ Save Treatment</button>
                    <button type="button" class="btn-toggle-rx" onclick="toggleTreatmentForm()">Cancel</button>
                </div>
            </form>
        </div>

        {{-- Billing Table --}}
        <div style="overflow-x:auto">
            @if($billingEntries->count() > 0)
            <table class="billing-table" id="billingTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($billingEntries as $entry)
                    <tr data-type="{{ $entry->type }}" data-paid="{{ $entry->is_paid ? 'paid' : 'unpaid' }}">
                        <td style="white-space:nowrap;color:#94a3b8;font-size:13px">{{ $entry->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="type-badge type-{{ $entry->type }}">{{ ucfirst(str_replace('_', ' ', $entry->type)) }}</span>
                            @if($entry->is_past_note)<span class="note-badge" style="margin-left:4px">Past Note</span>@endif
                        </td>
                        <td>
                            {{ $entry->description ?: ($entry->treatment->name ?? '—') }}
                        </td>
                        <td>
                            @if($entry->is_past_note)
                                <span style="color:#cbd5e1;font-size:12px;font-style:italic">Note only</span>
                            @else
                                <span style="font-weight:700;color:{{ $entry->type === 'custom_expense' ? '#dc2626' : '#1e293b' }}">
                                    {{ $entry->type === 'custom_expense' ? '-' : '' }}RM{{ number_format($entry->amount, 2) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($entry->is_past_note)
                                <span class="note-badge">Note</span>
                            @elseif($entry->is_paid)
                                <span class="paid-badge">✓ Paid</span>
                                @if($entry->paid_at)
                                    <div style="font-size:11px;color:#94a3b8;margin-top:2px">{{ $entry->paid_at->format('d M Y') }}</div>
                                @endif
                            @else
                                <span class="unpaid-badge">Unpaid</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap">
                            @if(!$entry->is_paid && !$entry->is_past_note)
                                <button class="btn-pay" onclick="markPaid({{ $patient->id }}, {{ $entry->id }}, this)">✓ Mark Paid</button>
                            @endif
                            <button class="btn-del-entry" onclick="deleteEntry({{ $patient->id }}, {{ $entry->id }}, this)">✕</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                <div style="font-size:15px;font-weight:600;color:#64748b;margin-bottom:4px">No billing entries yet</div>
                <div style="font-size:13px">Click "+ Add Entry" to get started</div>
            </div>
            @endif
        </div>
    </div>

    {{-- ════════════ PRESCRIPTIONS CARD ════════════ --}}
    <div class="section-card">
        <div class="section-header">
            <h6>
                <div class="section-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V5a2 2 0 0 0-2-2zm-1 11h-4v4h-4v-4H6v-4h4V6h4v4h4v4z"/></svg>
                </div>
                Prescriptions
            </h6>
            <span style="background:#eff6ff;color:#1363C6;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">
                {{ $prescriptions->count() }} total
            </span>
        </div>

        {{-- ✅ FIXED: Prescription uses new system via booking-specific links --}}
        @if(isset($bookings) && $bookings->count() > 0)
        <div style="padding:16px 24px;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
            <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px;">
                📋 Add Prescription — Select a Booking:
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @foreach($bookings->take(5) as $bk)
                <a href="{{ route('hospital_admin.prescriptions.create', $bk->id) }}"
                   class="rx-booking-btn {{ $bk->status }}">
                    {{ \Carbon\Carbon::parse($bk->booking_date)->format('d M Y') }}
                    &nbsp;·&nbsp; {{ ucfirst($bk->status) }}
                    @if($bk->doctor_name) &nbsp;·&nbsp; Dr. {{ $bk->doctor_name }} @endif
                </a>
                @endforeach
            </div>
        </div>
        @else
        <div style="padding:12px 24px;background:#f8fafc;border-bottom:1px solid #f1f5f9;">
            <span style="font-size:13px;color:#94a3b8;">No bookings found for this patient. Create a booking first to add a prescription.</span>
        </div>
        @endif

        {{-- ✅ FIXED: Prescription list uses new system (items.medicine) not old columns --}}
        <div style="padding:20px 24px">
            @if($prescriptions->count() > 0)
                @foreach($prescriptions as $rx)
                <div class="rx-card">
                    {{-- Booking tag --}}
                    @php $linkedBooking = isset($bookings) ? $bookings->firstWhere('id', $rx->booking_id) : null; @endphp
                    @if($linkedBooking)
                        <div class="rx-booking-tag">
                            📅 {{ \Carbon\Carbon::parse($linkedBooking->booking_date)->format('d M Y') }}
                            @if($linkedBooking->doctor_name) — Dr. {{ $linkedBooking->doctor_name }} @endif
                        </div>
                    @endif

                    {{-- Medicine items (new system) --}}
                    @if($rx->items && $rx->items->count() > 0)
                        @foreach($rx->items as $item)
                        <div class="rx-medicine-row">
                            <div>
                                <span style="font-weight:700;color:#1e293b;">💊 {{ $item->medicine->name ?? 'Unknown medicine' }}</span>
                                <span style="color:#64748b;font-size:13px;margin-left:8px;">× {{ $item->quantity }} {{ $item->medicine->unit ?? '' }}</span>
                                @if($item->dosage_instructions)
                                    <span style="color:#94a3b8;font-size:12px;margin-left:8px;">— {{ $item->dosage_instructions }}</span>
                                @endif
                            </div>
                            <span style="font-weight:700;color:#1363C6;font-size:14px;">RM{{ number_format($item->lineTotal(), 2) }}</span>
                        </div>
                        @endforeach
                        <div style="text-align:right;font-weight:800;color:#1e293b;margin-top:8px;padding-top:8px;border-top:1px solid #f1f5f9;">
                            Total: RM{{ number_format($rx->totalCost(), 2) }}
                        </div>
                    @else
                        {{-- Fallback: show old-style columns if items are empty (legacy data) --}}
                        @if($rx->medicine_name ?? null)
                        <div class="rx-medicine-row">
                            <span style="font-weight:700;">💊 {{ $rx->medicine_name }}</span>
                            <span style="color:#64748b;font-size:13px;">
                                {{ $rx->dosage ?? '' }}
                                @if($rx->frequency ?? null) · {{ $rx->frequency }}@endif
                                @if($rx->duration ?? null) · {{ $rx->duration }}@endif
                            </span>
                        </div>
                        @if($rx->instructions ?? null)
                            <div class="rx-notes">📝 {{ $rx->instructions }}</div>
                        @endif
                        @else
                        <span style="color:#94a3b8;font-size:13px;">No medicine details recorded.</span>
                        @endif
                    @endif

                    @if($rx->notes)
                        <div class="rx-notes" style="margin-top:8px;">📝 {{ $rx->notes }}</div>
                    @endif
                    <div style="font-size:12px;color:#cbd5e1;margin-top:6px;">
                        Added {{ \Carbon\Carbon::parse($rx->created_at)->format('d M Y') }}
                    </div>
                </div>
                @endforeach
            @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V5a2 2 0 0 0-2-2zm-1 11h-4v4h-4v-4H6v-4h4V6h4v4h4v4z"/></svg>
                    <div style="font-size:15px;font-weight:600;color:#64748b;margin-bottom:4px">No prescriptions yet</div>
                    <div style="font-size:13px">Select a booking above to add a prescription</div>
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════ BOOKING HISTORY CARD ════════════ --}}
    <div class="section-card">
        <div class="section-header">
            <h6>
                <div class="section-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                </div>
                Booking History
            </h6>
            <span style="background:#eff6ff;color:#1363C6;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">
                {{ isset($bookings) ? $bookings->count() : 0 }} total
            </span>
        </div>

        @if(isset($bookings) && $bookings->count() > 0)
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
                        <th>Prescription</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td><span style="font-family:monospace;font-weight:700;color:#1363C6;font-size:13px">{{ $booking->action_token }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}</td>
                        <td><span style="font-weight:600">{{ $booking->doctor_name ?? '—' }}</span></td>
                        <td style="max-width:180px"><span style="color:#64748b">{{ ($booking->cause ?? null) ? Str::limit($booking->cause, 40) : '—' }}</span></td>
                        <td><span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></td>
                        <td>
                            <a href="{{ route('hospital_admin.prescriptions.create', $booking->id) }}"
                               style="font-size:12px;color:#1363C6;font-weight:600;text-decoration:none;">
                                📋 Add
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
            <div style="font-size:15px;font-weight:600;color:#64748b;margin-bottom:4px">No bookings yet</div>
            <div style="font-size:13px">This patient has no booking history</div>
        </div>
        @endif
    </div>

</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function enableEdit()  { document.body.classList.add('editing'); }
function disableEdit() { document.body.classList.remove('editing'); }

function toggleBillingForm() {
    document.getElementById('billingForm').classList.toggle('open');
    document.getElementById('treatmentForm').classList.remove('open');
}

function toggleTreatmentForm() {
    document.getElementById('treatmentForm').classList.toggle('open');
    document.getElementById('billingForm').classList.remove('open');
}

function fillTreatmentPrice() {
    const select = document.getElementById('treatmentSelect');
    const option = select.options[select.selectedIndex];

    const price = parseFloat(option?.dataset?.price || 0);
    const name  = option?.dataset?.name || '';

    document.getElementById('treatmentUnitPrice').value = price.toFixed(2);

    const desc = document.getElementById('treatmentDescription');
    if (!desc.value && name) {
        desc.value = name;
    }

    recalculateTreatmentTotal();
}

function recalculateTreatmentTotal() {
    const qty = parseFloat(document.getElementById('treatmentQuantity').value || 1);
    const unitPrice = parseFloat(document.getElementById('treatmentUnitPrice').value || 0);
    const discount = parseFloat(document.getElementById('treatmentDiscount').value || 0);
    const isPast = document.getElementById('isPastRecord').checked;

    let total = (qty * unitPrice) - discount;
    if (total < 0) total = 0;
    if (isPast) total = 0;

    document.getElementById('treatmentAmountInput').value = total.toFixed(2);
}

function toggleTreatmentAmount(checkbox) {
    const hidden = document.getElementById('isPastNoteHidden');
    hidden.value = checkbox.checked ? '1' : '0';
    recalculateTreatmentTotal();
}

function filterBilling(type, btn) {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('#billingTable tbody tr').forEach(row => {
        if (type === 'all') row.style.display = '';
        else if (type === 'paid' || type === 'unpaid') row.style.display = row.dataset.paid === type ? '' : 'none';
        else row.style.display = row.dataset.type === type ? '' : 'none';
    });
}

function markPaid(patientId, entryId, btn) {
    if (!confirm('Mark this entry as paid?')) return;
    fetch(`/hospital_admin/patients/${patientId}/billing/${entryId}/pay`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert('Failed to mark as paid.');
    })
    .catch(() => alert('Network error. Please try again.'));
}

function deleteEntry(patientId, entryId, btn) {
    if (!confirm('Delete this billing entry?')) return;
    fetch(`/hospital_admin/patients/${patientId}/billing/${entryId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) btn.closest('tr').remove();
        else alert('Failed to delete entry.');
    })
    .catch(() => alert('Network error. Please try again.'));
}
</script>

@endsection