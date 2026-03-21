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

    .rx-card {
        border: 1.5px solid #e2e8f0; border-radius: 12px;
        padding: 16px 20px; margin-bottom: 12px;
        display: flex; align-items: flex-start; justify-content: space-between;
        gap: 12px; transition: border-color 0.2s;
    }
    .rx-card:hover { border-color: #1363C6; }
    .rx-name { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .rx-meta { font-size: 13px; color: #64748b; }
    .rx-notes { font-size: 13px; color: #94a3b8; margin-top: 4px; font-style: italic; }
    .rx-booking-tag {
        display: inline-block; font-size: 11px; font-weight: 600;
        background: #eff6ff; color: #1363C6;
        padding: 2px 8px; border-radius: 20px; margin-bottom: 6px;
    }
    .btn-delete-rx {
        padding: 5px 12px; background: #fee2e2; color: #991b1b;
        border: none; border-radius: 8px; font-size: 12px;
        font-weight: 600; cursor: pointer; white-space: nowrap;
        transition: background 0.2s; flex-shrink: 0;
    }
    .btn-delete-rx:hover { background: #fecaca; }

    .rx-form { padding: 20px 24px; border-top: 1px solid #f1f5f9; display: none; }
    .rx-form.open { display: block; }
    .rx-grid {
        display: grid; grid-template-columns: 1fr 1fr 1fr;
        gap: 14px; margin-bottom: 14px;
    }
    .rx-field label {
        display: block; font-size: 11px; font-weight: 700;
        color: #94a3b8; text-transform: uppercase;
        letter-spacing: 0.05em; margin-bottom: 5px;
    }
    .rx-field input, .rx-field select, .rx-field textarea {
        width: 100%; padding: 9px 12px;
        border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 14px; color: #1e293b; outline: none;
        transition: border-color 0.2s; font-family: inherit;
        background: #fff;
    }
    .rx-field input:focus, .rx-field select:focus, .rx-field textarea:focus {
        border-color: #1363C6;
        box-shadow: 0 0 0 3px rgba(19,99,198,0.08);
    }
    .rx-field input[readonly] {
        background: #f0f7ff; color: #1363C6;
        font-weight: 600; cursor: not-allowed;
    }
    .btn-add-rx {
        padding: 8px 18px; background: #1363C6; color: #fff;
        border: none; border-radius: 8px; font-size: 13px;
        font-weight: 600; cursor: pointer; transition: background 0.2s;
    }
    .btn-add-rx:hover { background: #0f52a8; }
    .btn-toggle-rx {
        padding: 7px 16px; background: #eff6ff; color: #1363C6;
        border: 1.5px solid #bfdbfe; border-radius: 8px; font-size: 13px;
        font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .btn-toggle-rx:hover { background: #dbeafe; }

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

    @media (max-width: 768px) {
        .info-grid { grid-template-columns: repeat(2, 1fr); }
        .rx-grid { grid-template-columns: 1fr 1fr; }
        .profile-top { flex-direction: column; text-align: center; }
    }
    @media (max-width: 480px) {
        .info-grid { grid-template-columns: 1fr; }
        .rx-grid { grid-template-columns: 1fr; }
    }
</style>

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
            @foreach($errors->all() as $error)
                <div>✕ {{ $error }}</div>
            @endforeach
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

    {{-- PROFILE CARD --}}
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

            <div class="section-divider">
                <div class="section-divider-label">Personal Information</div>
            </div>
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

            <div class="section-divider">
                <div class="section-divider-label">Address</div>
            </div>
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

            <div class="section-divider">
                <div class="section-divider-label">Emergency Contact</div>
            </div>
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

    {{-- PRESCRIPTIONS --}}
    <div class="section-card">
        <div class="section-header">
            <h6>
                <div class="section-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V5a2 2 0 0 0-2-2zm-1 11h-4v4h-4v-4H6v-4h4V6h4v4h4v4z"/></svg>
                </div>
                Prescriptions
            </h6>
            <div style="display:flex;align-items:center;gap:10px">
                <span style="background:#eff6ff;color:#1363C6;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">
                    {{ $prescriptions->count() }} total
                </span>
                <button type="button" class="btn-toggle-rx" onclick="toggleRxForm()">+ Add Prescription</button>
            </div>
        </div>

        {{-- Add Prescription Form --}}
        <div class="rx-form" id="rxForm">
            <form method="POST" action="{{ route('hospital_admin.patients.prescriptions.add', $patient->id) }}">
                @csrf

                {{-- Auto-assign latest booking silently --}}
                @if($bookings->count() > 0)
                    <input type="hidden" name="booking_id" value="{{ $bookings->first()->id }}">
                @endif

                <div class="rx-grid">

                    {{-- Medicine Dropdown --}}
                    <div class="rx-field">
                        <label>Medicine Name *</label>
                        <select name="medicine_id" id="patient_medicine_select" required>
                            <option value="">— Select Medicine —</option>
                            @foreach($medicines as $medicine)
                                <option value="{{ $medicine->id }}"
                                        data-price="{{ $medicine->price }}"
                                        data-dosage="{{ $medicine->dosage ?? '' }}">
                                    {{ $medicine->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dosage — auto filled from medicine, editable --}}
                    <div class="rx-field">
                        <label>Dosage *</label>
                        <input type="text" name="dosage" id="patient_dosage"
                               placeholder="e.g. 500mg" required>
                    </div>

                    {{-- Frequency --}}
                    <div class="rx-field">
                        <label>Frequency *</label>
                        <input type="text" name="frequency"
                               placeholder="e.g. Twice daily" required>
                    </div>

                    {{-- Duration --}}
                    <div class="rx-field">
                        <label>Duration</label>
                        <input type="text" name="duration"
                               placeholder="e.g. 7 days">
                    </div>

                    {{-- Price — auto filled, read only --}}
                    <div class="rx-field">
                        <label>Price (RM)</label>
                        <input type="text" id="patient_medicine_price"
                               placeholder="Auto filled" readonly>
                    </div>

                    {{-- Instructions --}}
                    <div class="rx-field" style="grid-column: span 3">
                        <label>Instructions</label>
                        <input type="text" name="instructions"
                               placeholder="e.g. Take after meals">
                    </div>

                </div>

                <div style="display:flex;gap:10px">
                    <button type="submit" class="btn-add-rx">+ Save Prescription</button>
                    <button type="button" class="btn-toggle-rx" onclick="toggleRxForm()">Cancel</button>
                </div>
            </form>
        </div>

        {{-- Prescription List --}}
        <div style="padding: 20px 24px">
            @if($prescriptions->count() > 0)
                @foreach($prescriptions as $rx)
                    <div class="rx-card">
                        <div style="flex:1">
                            @php
                                $linkedBooking = $bookings->firstWhere('id', $rx->booking_id);
                            @endphp
                            @if($linkedBooking)
                                <div class="rx-booking-tag">
                                    📅 {{ \Carbon\Carbon::parse($linkedBooking->booking_date)->format('d M Y') }}
                                    — {{ $linkedBooking->doctor_name ?? 'No doctor' }}
                                </div>
                            @endif
                            <div class="rx-name">💊 {{ $rx->medicine_name }}</div>
                            <div class="rx-meta">
                                <strong>Dosage:</strong> {{ $rx->dosage ?? '—' }} &nbsp;·&nbsp;
                                <strong>Frequency:</strong> {{ $rx->frequency ?? '—' }}
                                @if($rx->duration ?? null) &nbsp;·&nbsp; <strong>Duration:</strong> {{ $rx->duration }} @endif
                            </div>
                            @if($rx->instructions ?? null)
                                <div class="rx-notes">📝 {{ $rx->instructions }}</div>
                            @endif
                            <div style="font-size:12px;color:#cbd5e1;margin-top:4px">
                                Added {{ \Carbon\Carbon::parse($rx->created_at)->format('d M Y') }}
                            </div>
                        </div>
                        <form method="POST"
                              action="{{ route('hospital_admin.patients.prescriptions.delete', [$patient->id, $rx->id]) }}"
                              onsubmit="return confirm('Delete this prescription?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete-rx">✕ Delete</button>
                        </form>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V5a2 2 0 0 0-2-2zm-1 11h-4v4h-4v-4H6v-4h4V6h4v4h4v4z"/></svg>
                    <div style="font-size:15px;font-weight:600;color:#64748b;margin-bottom:4px">No prescriptions yet</div>
                    <div style="font-size:13px">Click "+ Add Prescription" to add one</div>
                </div>
            @endif
        </div>
    </div>

    {{-- BOOKING HISTORY --}}
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
                                <td><span style="font-weight:600">{{ $booking->doctor_name ?? '—' }}</span></td>
                                <td style="max-width:180px">
                                    <span style="color:#64748b">{{ ($booking->cause ?? null) ? Str::limit($booking->cause, 40) : '—' }}</span>
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
            <div class="empty-state">
                <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                <div style="font-size:15px;font-weight:600;color:#64748b;margin-bottom:4px">No bookings yet</div>
                <div style="font-size:13px">This patient has no booking history</div>
            </div>
        @endif
    </div>

</div>

<script>
    function enableEdit()  { document.body.classList.add('editing'); }
    function disableEdit() { document.body.classList.remove('editing'); }
    function toggleRxForm() { document.getElementById('rxForm').classList.toggle('open'); }

    document.getElementById("patient_medicine_select").addEventListener("change", function () {
        const selected = this.options[this.selectedIndex];
        const price    = selected.getAttribute("data-price");
        const dosage   = selected.getAttribute("data-dosage");

        document.getElementById("patient_medicine_price").value =
            price ? parseFloat(price).toFixed(2) : "";
        document.getElementById("patient_dosage").value =
            dosage ? dosage : "";
    });
</script>

@endsection