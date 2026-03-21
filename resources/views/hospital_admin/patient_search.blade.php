@extends('layouts.app1')
@section('title', 'Patient Search')
@section('content')

<style>
    .search-hero {
        background: linear-gradient(135deg, #1363C6 0%, #0a3d8f 100%);
        padding: 40px 0 60px;
        margin-bottom: -30px;
    }
    .search-hero h1 {
        color: #fff;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .search-hero p { color: rgba(255,255,255,0.75); font-size: 15px; }

    .search-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(19,99,198,0.12);
        padding: 28px 30px;
        margin-bottom: 30px;
    }

    .search-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 14px;
        align-items: end;
    }

    .field-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 6px;
    }

    .search-input {
        width: 100%;
        padding: 11px 14px 11px 38px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        color: #1e293b;
        background: #f8fafc;
        transition: all 0.2s ease;
        outline: none;
    }
    .search-input:focus {
        border-color: #1363C6;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(19,99,198,0.08);
    }
    .input-wrap { position: relative; }
    .input-wrap svg {
        position: absolute; left: 11px; top: 50%;
        transform: translateY(-50%);
        width: 16px; height: 16px; fill: #94a3b8;
        pointer-events: none;
    }

    .btn-search {
        padding: 11px 28px;
        background: #1363C6;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        display: flex; align-items: center; gap: 7px;
    }
    .btn-search:hover { background: #0f52a8; transform: translateY(-1px); }
    .btn-clear {
        padding: 11px 18px;
        background: #f1f5f9;
        color: #64748b;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex; align-items: center;
    }
    .btn-clear:hover { background: #e2e8f0; color: #334155; }

    /* RESULTS TABLE */
    .results-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .results-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .results-header h6 {
        font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;
    }
    .badge-count {
        background: #eff6ff; color: #1363C6;
        padding: 3px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 700;
    }

    table.patient-table { width: 100%; border-collapse: collapse; }
    table.patient-table thead tr {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    table.patient-table th {
        padding: 12px 20px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    table.patient-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s ease;
        cursor: pointer;
    }
    table.patient-table tbody tr:hover { background: #f0f7ff; }
    table.patient-table td {
        padding: 14px 20px;
        font-size: 14px;
        color: #334155;
        vertical-align: middle;
    }

    .avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1363C6, #4a90e2);
        color: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 14px;
        margin-right: 10px; flex-shrink: 0;
    }
    .patient-name-cell { display: flex; align-items: center; }

    .gender-badge {
        padding: 3px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
    }
    .gender-male   { background: #eff6ff; color: #1d4ed8; }
    .gender-female { background: #fdf2f8; color: #9d174d; }
    .gender-other  { background: #f0fdf4; color: #166534; }

    .btn-view {
        padding: 6px 14px;
        background: #1363C6;
        color: #fff;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-block;
    }
    .btn-view:hover { background: #0f52a8; color: #fff; }

    /* EMPTY STATE */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
    .empty-state svg {
        width: 56px; height: 56px;
        fill: #cbd5e1; margin-bottom: 16px;
    }
    .empty-state h6 { font-size: 16px; color: #64748b; margin-bottom: 6px; }
    .empty-state p  { font-size: 14px; }

    @media (max-width: 768px) {
        .search-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="search-hero">
    <div class="container">
        <h1><i class="fas fa-users me-2"></i>Patient Search</h1>
        <p>Search by name, phone number, or IC/Passport to find patient records</p>
    </div>
</div>

<div class="container pb-5">

    {{-- SEARCH FORM --}}
    <div class="search-card">
        <form method="GET" action="{{ route('hospital_admin.patients.search') }}" id="searchForm">
            <div class="search-grid">

                {{-- Name --}}
                <div>
                    <div class="field-label">Patient Name</div>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        <input type="text" name="name" class="search-input"
                               placeholder="e.g. Ahmad bin Ali"
                               value="{{ request('name') }}">
                    </div>
                </div>

                {{-- Phone --}}
                <div>
                    <div class="field-label">Phone Number</div>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                        <input type="text" name="phone" class="search-input"
                               placeholder="e.g. 60123456789"
                               value="{{ request('phone') }}">
                    </div>
                </div>

                {{-- IC / Passport --}}
                <div>
                    <div class="field-label">IC / Passport No</div>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8h16v10zM6 10h2v2H6zm0 4h8v2H6zm10 0h2v2h-2zm0-4h2v2h-2z"/></svg>
                        <input type="text" name="ic_passport" class="search-input"
                               placeholder="e.g. 901231-14-5678"
                               value="{{ request('ic_passport') }}">
                    </div>
                </div>

                {{-- Buttons --}}
                <div style="display:flex;gap:8px;align-items:flex-end">
                    <button type="submit" class="btn-search">
                        <svg style="width:15px;height:15px;fill:white" viewBox="0 0 24 24">
                            <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        Search
                    </button>
                    @if(request('name') || request('phone') || request('ic_passport'))
                        <a href="{{ route('hospital_admin.patients.search') }}" class="btn-clear">✕ Clear</a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    {{-- RESULTS --}}
    <div class="results-card">
        <div class="results-header">
            <h6>
                <i class="fas fa-list me-2 text-primary"></i>
                {{ (request('name') || request('phone') || request('ic_passport')) ? 'Search Results' : 'All Patients' }}
            </h6>
            <span class="badge-count">{{ $patients->count() }} found</span>
        </div>

        @if($patients->count() > 0)
            <div style="overflow-x:auto">
                <table class="patient-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>IC / Passport</th>
                            <th>Phone</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Bookings</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            <tr onclick="window.location='{{ route('hospital_admin.patients.show', $patient->id) }}'">
                                <td>
                                    <div class="patient-name-cell">
                                        <div class="avatar">{{ strtoupper(substr($patient->name, 0, 1)) }}</div>
                                        <div>
                                            <div style="font-weight:600;color:#1e293b">{{ $patient->name }}</div>
                                            <div style="font-size:12px;color:#94a3b8">{{ $patient->nationality ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $patient->ic_passport_no ?? '—' }}</td>
                                <td>{{ $patient->phone_no }}</td>
                                <td>{{ $patient->age ?? '—' }}</td>
                                <td>
                                    @if($patient->gender)
                                        <span class="gender-badge gender-{{ $patient->gender }}">
                                            {{ ucfirst($patient->gender) }}
                                        </span>
                                    @else
                                        <span style="color:#cbd5e1">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-weight:700;color:#1363C6">{{ $patient->bookings_count }}</span>
                                    <span style="color:#94a3b8;font-size:12px"> visits</span>
                                </td>
                                <td>
                                    <a href="{{ route('hospital_admin.patients.show', $patient->id) }}"
                                       class="btn-view" onclick="event.stopPropagation()">
                                        View →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <h6>No patients found</h6>
                <p>Try a different name, phone number, or IC/Passport number</p>
            </div>
        @endif
    </div>

</div>

@endsection