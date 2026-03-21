@extends('layouts.app1')
@section('title', 'Prescriptions')
@section('content')

<style>
    .rx-hero {
        background: linear-gradient(135deg, #1363C6 0%, #0a3d8f 100%);
        padding: 32px 0 60px;
        margin-bottom: -36px;
    }
    .back-btn {
        display: inline-flex; align-items: center; gap: 6px;
        color: rgba(255,255,255,0.8); text-decoration: none;
        font-size: 14px; font-weight: 500; margin-bottom: 18px;
        transition: color 0.2s;
    }
    .back-btn:hover { color: #fff; }
    .rx-card {
        background: #fff; border-radius: 16px;
        box-shadow: 0 6px 30px rgba(19,99,198,0.10);
        margin-bottom: 24px; overflow: hidden;
    }
    .rx-card-header {
        padding: 18px 24px; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .rx-card-header h6 {
        font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;
        display: flex; align-items: center; gap: 8px;
    }
    .rx-body { padding: 24px; }
    .booking-meta {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
    }
    .meta-label {
        font-size: 11px; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;
    }
    .meta-value { font-size: 14px; font-weight: 600; color: #1e293b; }
    .allergy-banner {
        background: #fff7ed; border: 1.5px solid #fed7aa;
        border-radius: 12px; padding: 14px 18px;
        display: flex; align-items: flex-start; gap: 12px;
        margin-bottom: 24px;
    }
    .allergy-icon {
        width: 36px; height: 36px; flex-shrink: 0;
        background: #fef3c7; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }
    .allergy-title { font-size: 13px; font-weight: 700; color: #92400e; margin-bottom: 3px; }
    .allergy-text  { font-size: 13px; color: #b45309; }
    table.rx-table { width: 100%; border-collapse: collapse; }
    table.rx-table thead tr { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
    table.rx-table th {
        padding: 11px 16px; text-align: left; font-size: 12px;
        font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.04em;
    }
    table.rx-table tbody tr { border-bottom: 1px solid #f1f5f9; }
    table.rx-table tbody tr:last-child { border-bottom: none; }
    table.rx-table td { padding: 13px 16px; font-size: 14px; color: #334155; vertical-align: middle; }
    .rx-num {
        width: 28px; height: 28px; background: #eff6ff; color: #1363C6;
        border-radius: 50%; display: inline-flex;
        align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700;
    }
    .add-form { padding: 24px; }
    .form-grid-rx {
        display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
        gap: 14px; margin-bottom: 14px;
    }
    .fl {
        font-size: 12px; font-weight: 600; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 5px;
    }
    .fi {
        width: 100%; padding: 10px 12px;
        border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-size: 14px; color: #1e293b; background: #f8fafc;
        outline: none; transition: all 0.2s; font-family: inherit;
    }
    .fi:focus {
        border-color: #1363C6; background: #fff;
        box-shadow: 0 0 0 3px rgba(19,99,198,0.08);
    }
    textarea.fi { resize: vertical; min-height: 70px; }
    .btn-add {
        background: #1363C6; color: #fff; border: none;
        padding: 10px 24px; border-radius: 10px;
        font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .btn-add:hover { background: #0f52a8; }
    .btn-del {
        background: #fee2e2; color: #dc2626; border: none;
        padding: 5px 11px; border-radius: 7px;
        font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .btn-del:hover { background: #fecaca; }
    .btn-edit-rx {
        background: #eff6ff; color: #1363C6; border: none;
        padding: 5px 11px; border-radius: 7px;
        font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;
        text-decoration: none; display: inline-block;
    }
    .btn-edit-rx:hover { background: #dbeafe; color: #1363C6; }
    .btn-print {
        background: #f0fdf4; color: #16a34a;
        border: 1.5px solid #bbf7d0; padding: 8px 18px;
        border-radius: 10px; font-size: 13px; font-weight: 600;
        text-decoration: none; display: inline-flex;
        align-items: center; gap: 6px; transition: all 0.2s;
    }
    .btn-print:hover { background: #dcfce7; color: #16a34a; }
    .empty-rx { text-align: center; padding: 40px; color: #94a3b8; }
    .modal-backdrop-rx {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.4); z-index: 1000;
        align-items: center; justify-content: center;
    }
    .modal-backdrop-rx.open { display: flex; }
    .modal-box {
        background: #fff; border-radius: 16px; padding: 28px;
        width: 100%; max-width: 540px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .modal-box h6 { font-size: 16px; font-weight: 700; margin-bottom: 20px; color: #1e293b; }
    @media (max-width: 768px) {
        .booking-meta { grid-template-columns: repeat(2,1fr); }
        .form-grid-rx { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 480px) {
        .form-grid-rx { grid-template-columns: 1fr; }
        .booking-meta { grid-template-columns: 1fr; }
    }
</style>

<div class="rx-hero">
    <div class="container">
        <a href="{{ url()->previous() }}" class="back-btn">← Back</a>
        <div style="color:#fff">
            <div style="font-size:13px;opacity:0.7;margin-bottom:4px">Prescription Management</div>
            <div style="font-size:24px;font-weight:700">{{ $booking->patient_name }}</div>
        </div>
    </div>
</div>

<div class="container pb-5">

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;
                    border-radius:10px;padding:12px 18px;margin-bottom:20px;
                    font-size:14px;font-weight:600;">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;
                    border-radius:10px;padding:12px 18px;margin-bottom:20px;font-size:14px;">
            @foreach($errors->all() as $error)
                <div>✕ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- BOOKING INFO --}}
    <div class="rx-card">
        <div class="rx-card-header">
            <h6>📋 Booking Details</h6>
            @if($prescriptions->count() > 0)
                <a href="{{ route('prescriptions.print', $booking->id) }}"
                   target="_blank" class="btn-print">
                    🖨️ Print / Save PDF
                </a>
            @endif
        </div>
        <div class="rx-body">
            <div class="booking-meta">
                <div>
                    <div class="meta-label">Patient</div>
                    <div class="meta-value">{{ $booking->patient_name }}</div>
                </div>
                <div>
                    <div class="meta-label">Date</div>
                    <div class="meta-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="meta-label">Time</div>
                    <div class="meta-value">{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}</div>
                </div>
                <div>
                    <div class="meta-label">Doctor</div>
                    <div class="meta-value">{{ $booking->doctor_name ?? '—' }}</div>
                </div>
                <div>
                    <div class="meta-label">Phone</div>
                    <div class="meta-value">{{ $booking->patient_phone }}</div>
                </div>
                <div>
                    <div class="meta-label">Age</div>
                    <div class="meta-value">{{ $booking->age ? $booking->age . ' yrs' : '—' }}</div>
                </div>
                <div>
                    <div class="meta-label">Booking Code</div>
                    <div class="meta-value" style="font-family:monospace;color:#1363C6">
                        {{ $booking->action_token }}
                    </div>
                </div>
                <div>
                    <div class="meta-label">Complaint</div>
                    <div class="meta-value">{{ $booking->cause ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ALLERGY FLAG --}}
    @if($patient && !empty($patient->allergy))
        <div class="allergy-banner">
            <div class="allergy-icon">⚠️</div>
            <div>
                <div class="allergy-title">⚠️ Allergy / Medical History Alert</div>
                <div class="allergy-text">{{ $patient->allergy }}</div>
            </div>
        </div>
    @endif

    {{-- CURRENT PRESCRIPTIONS --}}
    <div class="rx-card">
        <div class="rx-card-header">
            <h6>💊 Prescriptions
                <span style="background:#eff6ff;color:#1363C6;padding:2px 10px;
                             border-radius:20px;font-size:12px;font-weight:700;">
                    {{ $prescriptions->count() }}
                </span>
            </h6>
        </div>

        @if($prescriptions->count() > 0)
            <div style="overflow-x:auto">
                <table class="rx-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Medicine</th>
                            <th>Dosage</th>
                            <th>Frequency</th>
                            <th>Duration</th>
                            <th>Instructions</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prescriptions as $i => $rx)
                            <tr>
                                <td><span class="rx-num">{{ $i + 1 }}</span></td>
                                <td style="font-weight:600">{{ $rx->medicine_name }}</td>
                                <td>{{ $rx->dosage ?? '—' }}</td>
                                <td>{{ $rx->frequency ?? '—' }}</td>
                                <td>{{ $rx->duration ?? '—' }}</td>
                                <td style="color:#64748b;font-size:13px">{{ $rx->instructions ?? '—' }}</td>
                                <td style="white-space:nowrap">
                                    <button class="btn-edit-rx me-1"
                                            onclick="openEdit(
                                                {{ $rx->id }},
                                                '{{ addslashes($rx->medicine_name) }}',
                                                '{{ addslashes($rx->dosage) }}',
                                                '{{ addslashes($rx->frequency) }}',
                                                '{{ addslashes($rx->duration) }}',
                                                '{{ addslashes($rx->instructions) }}'
                                            )">Edit</button>
                                    <form method="POST"
                                          action="{{ route('prescriptions.destroy', $rx->id) }}"
                                          style="display:inline"
                                          onsubmit="return confirm('Remove this prescription?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-del">✕</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-rx">
                <div style="font-size:36px;margin-bottom:10px">💊</div>
                <div style="font-size:15px;font-weight:600;color:#64748b;margin-bottom:4px">No prescriptions yet</div>
                <div style="font-size:13px">Add the first prescription below</div>
            </div>
        @endif
    </div>

    {{-- ADD NEW PRESCRIPTION --}}
    <div class="rx-card">
        <div class="rx-card-header">
            <h6>➕ Add Prescription</h6>
        </div>
        <div class="add-form">
            <form method="POST" action="{{ route('prescriptions.store', $booking->id) }}">
                @csrf
                <div class="form-grid-rx">

                    {{-- Medicine dropdown --}}
                    <div>
                        <div class="fl">Medicine *</div>
                        <select name="medicine_id" id="medicine_select" class="fi" required>
                            <option value="">Select Medicine</option>
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
                    <div>
                        <div class="fl">Dosage</div>
                        <input type="text" name="dosage" id="dosage_input" class="fi"
                               placeholder="e.g. 500mg" value="{{ old('dosage') }}">
                    </div>

                    {{-- Frequency — open field --}}
                    <div>
                        <div class="fl">Frequency</div>
                        <input type="text" name="frequency" class="fi"
                               placeholder="e.g. Twice daily" value="{{ old('frequency') }}">
                    </div>

                    {{-- Duration — open field --}}
                    <div>
                        <div class="fl">Duration</div>
                        <input type="text" name="duration" class="fi"
                               placeholder="e.g. 5 days" value="{{ old('duration') }}">
                    </div>

                    {{-- Price — auto filled, read only --}}
                    <div>
                        <div class="fl">Price (RM)</div>
                        <input type="text" name="price" id="medicine_price" class="fi"
                               placeholder="Auto filled" readonly
                               style="background:#f0f7ff;color:#1363C6;font-weight:600;cursor:not-allowed;">
                    </div>

                </div>

                {{-- Instructions — open field --}}
                <div style="margin-bottom:16px">
                    <div class="fl">Special Instructions</div>
                    <textarea name="instructions" class="fi"
                              placeholder="e.g. Take after meals, avoid alcohol...">{{ old('instructions') }}</textarea>
                </div>

                <button type="submit" class="btn-add">+ Add Prescription</button>
            </form>
        </div>
    </div>

</div>

{{-- EDIT MODAL --}}
<div class="modal-backdrop-rx" id="editModal">
    <div class="modal-box">
        <h6>✏️ Edit Prescription</h6>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                <div>
                    <div class="fl">Medicine Name *</div>
                    <input type="text" name="medicine_name" id="edit_medicine" class="fi" required>
                </div>
                <div>
                    <div class="fl">Dosage</div>
                    <input type="text" name="dosage" id="edit_dosage" class="fi">
                </div>
                <div>
                    <div class="fl">Frequency</div>
                    <input type="text" name="frequency" id="edit_frequency" class="fi">
                </div>
                <div>
                    <div class="fl">Duration</div>
                    <input type="text" name="duration" id="edit_duration" class="fi">
                </div>
            </div>
            <div style="margin-bottom:18px">
                <div class="fl">Instructions</div>
                <textarea name="instructions" id="edit_instructions" class="fi"></textarea>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" class="btn-add">Save Changes</button>
                <button type="button" onclick="closeEdit()"
                        style="padding:10px 20px;border:1.5px solid #e2e8f0;border-radius:10px;
                               background:#fff;font-size:14px;font-weight:600;cursor:pointer;color:#64748b">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto-fill price and dosage when medicine is selected
    document.getElementById("medicine_select").addEventListener("change", function () {
        const selected = this.options[this.selectedIndex];
        const price    = selected.getAttribute("data-price");
        const dosage   = selected.getAttribute("data-dosage");

        document.getElementById("medicine_price").value = price
            ? parseFloat(price).toFixed(2) : "";
        document.getElementById("dosage_input").value = dosage
            ? dosage : "";
    });

    // Edit modal
    function openEdit(id, medicine, dosage, frequency, duration, instructions) {
        document.getElementById('edit_medicine').value     = medicine;
        document.getElementById('edit_dosage').value       = dosage;
        document.getElementById('edit_frequency').value    = frequency;
        document.getElementById('edit_duration').value     = duration;
        document.getElementById('edit_instructions').value = instructions;
        document.getElementById('editForm').action         = `/prescriptions/${id}`;
        document.getElementById('editModal').classList.add('open');
    }
    function closeEdit() {
        document.getElementById('editModal').classList.remove('open');
    }
    document.getElementById('editModal').addEventListener('click', function (e) {
        if (e.target === this) closeEdit();
    });
</script>

@endsection