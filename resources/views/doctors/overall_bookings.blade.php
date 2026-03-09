@extends('layouts.app1')
@section('title','Overall Patient Bookings')

@section('content')
<style>
/* =========================
   DOCTOR BOOKINGS – CLEAN PROFESSIONAL UI
   ========================= */

.booking-container {
    padding: 28px;
    background: #f4f7fb;
    min-height: 100vh;
}

/* ---------- Page Header ---------- */
.page-header {
    margin-bottom: 20px;
}

.page-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
}

.page-header p {
    color: #6b7280;
    font-size: 0.9rem;
    margin-top: 4px;
}

/* ---------- Search Box ---------- */
.search-box {
    background: #ffffff;
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 26px;
    box-shadow: 0 6px 16px rgba(19, 99, 198, 0.08);
}

.search-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.search-row .form-control {
    border-radius: 8px;
    font-size: 0.85rem;
}

/* ---------- Booking Grid ---------- */
.booking-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
    gap: 20px;
}

/* ---------- Booking Card ---------- */
.booking-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(19, 99, 198, 0.12);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.booking-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 34px rgba(19, 99, 198, 0.18);
}

/* ---------- Card Top ---------- */
.card-top {
    padding: 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eef2f7;
}

.date-box {
    background: #1363C6;
    color: #ffffff;
    padding: 10px 12px;
    border-radius: 10px;
    text-align: center;
    min-width: 60px;
}

.date-box div {
    font-size: 1rem;
    font-weight: 700;
}

.date-box small {
    font-size: 0.7rem;
    opacity: 0.9;
}

.card-top strong {
    display: block;
    margin-top: 6px;
    font-size: 0.85rem;
    color: #374151;
}

/* ---------- Status Badge ---------- */
.status-badge {
    padding: 5px 12px;
    border-radius: 16px;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.4px;
}

.status-badge.unverified {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.pending {
    background: #fff7ed;
    color: #c2410c;
}

.status-badge.accepted {
    background: #ecfdf5;
    color: #047857;
}

.status-badge.rejected {
    background: #fef2f2;
    color: #b91c1c;
}

.status-badge.cancelled {
    background: #f3f4f6;
    color: #374151;
}

.status-badge.no_show {
    background: #f3f4f6;
    color: #4b5563;
}

.status-badge.rescheduled {
    background: #eff6ff;
    color: #1d4ed8;
}

.status-badge.completed {
    background: #d1fae5;
    color: #065f46;
}

/* ---------- Card Body ---------- */
.card-body {
    padding: 18px;
}

/* ---------- Patient Row ---------- */
.patient-row {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 14px;
}

.patient-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #1363C6;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.95rem;
}

.patient-row strong {
    font-size: 0.9rem;
    color: #1f2937;
}

.patient-row small {
    color: #6b7280;
    font-size: 0.75rem;
}

.btn-secondary {
    background: #e6f0ff;
    color: #1363C6;
    border: 2px solid #1363C6;
}

.btn-secondary:hover {
    background: #1363C6;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(19, 99, 198, 0.25);
}

/* ---------- Info Chips ---------- */
.info-chip {
    background: #f1f5f9;
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 0.75rem;
    color: #374151;
    display: inline-block;
    margin: 3px 5px 5px 0;
}

/* ---------- Actions ---------- */
.action-btns {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    flex-wrap: wrap;
}

.btn-action {
    flex: 1;
    padding: 10px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 100px;
}

.btn-accept {
    background: #1363C6;
    color: #ffffff;
}

.btn-accept:hover {
    background: #0f52a5;
    transform: translateY(-1px);
}

.btn-reject {
    background: #ef4444;
    color: #ffffff;
}

.btn-reject:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.btn-reschedule {
    background: #3b82f6;
    color: #ffffff;
}

.btn-reschedule:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-noshow {
    background: #6b7280;
    color: #ffffff;
}

.btn-noshow:hover {
    background: #4b5563;
    transform: translateY(-1px);
}

.btn-completed {
    background: #10b981;
    color: #ffffff;
}

.btn-completed:hover {
    background: #059669;
    transform: translateY(-1px);
}

/* ---------- Status Messages ---------- */
.approved-msg,
.rejected-msg,
.completed-msg {
    text-align: center;
    padding: 12px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
}

/* ---------- Empty State ---------- */
.empty-state {
    text-align: center;
    padding: 60px 28px;
    color: #6b7280;
}

.empty-state h3 {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 4px;
}

/* ---------- Modal Styles ---------- */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: #ffffff;
    margin: auto;
    padding: 0;
    border-radius: 16px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    padding: 24px 28px;
    background: linear-gradient(135deg, #1363C6 0%, #0f52a5 100%);
    color: #ffffff;
    border-radius: 16px 16px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.4rem;
    font-weight: 700;
}

.close-btn {
    background: none;
    border: none;
    color: #ffffff;
    font-size: 1.8rem;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: background 0.2s ease;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

.modal-body {
    padding: 28px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #1363C6;
    box-shadow: 0 0 0 3px rgba(19, 99, 198, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-group small {
    display: block;
    color: #6b7280;
    font-size: 0.8rem;
    margin-top: 6px;
}

.modal-footer {
    padding: 20px 28px;
    background: #f9fafb;
    border-radius: 0 0 16px 16px;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.btn-modal {
    padding: 12px 24px;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-cancel {
    background: #e5e7eb;
    color: #374151;
}

.btn-cancel:hover {
    background: #d1d5db;
}

.btn-submit {
    background: #1363C6;
    color: #ffffff;
}

.btn-submit:hover {
    background: #0f52a5;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(19, 99, 198, 0.3);
}

.patient-info-box {
    background: #f0f9ff;
    border: 2px solid #bfdbfe;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
}

.patient-info-box h4 {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1e40af;
    margin: 0 0 8px 0;
}

.patient-info-box p {
    margin: 4px 0;
    font-size: 0.85rem;
    color: #1e3a8a;
}

.patient-info-box p strong {
    color: #1e40af;
}

</style>

<div class="booking-container">

<div class="page-header">
    <h1>📋 Patient Bookings</h1>
    <p>Showing {{ $booking_list->count() }} records</p>
</div>

{{--  SEARCH / FILTER --}}
<div class="search-box">
<form method="GET">
    <div class="search-row">
        <input type="date"
               name="date"
               value="{{ request('date') }}"
               class="form-control"
               style="max-width:200px"
               placeholder="Select Date">

        <select name="filter" class="form-control" style="max-width:240px">
            <option value="">Default (Pending + Today)</option>
            <option value="today" {{ request('filter')=='today'?'selected':'' }}>Today</option>
            <option value="unverified" {{ request('filter')=='unverified'?'selected':'' }}>Unverified</option>
            <option value="pending" {{ request('filter')=='pending'?'selected':'' }}>Pending</option>
            <option value="accepted" {{ request('filter')=='accepted'?'selected':'' }}>Accepted (Last 20)</option>
            <option value="rejected" {{ request('filter')=='rejected'?'selected':'' }}>Rejected (Last 20)</option>
            <option value="cancelled" {{ request('filter')=='cancelled'?'selected':'' }}>Cancelled (Last 20)</option>
            <option value="no_show" {{ request('filter')=='no_show'?'selected':'' }}>No Show (Last 20)</option>
            <option value="rescheduled" {{ request('filter')=='rescheduled'?'selected':'' }}>Rescheduled (Last 20)</option>
            <option value="completed" {{ request('filter')=='completed'?'selected':'' }}>Completed (Last 20)</option>
        </select>

        <button class="btn btn-primary">Search</button>
        <a href="{{ route('doctor.overall_bookings') }}" class="btn btn-secondary">Reset</a>
    </div>
</form>
</div>

{{-- 📋 BOOKINGS --}}
@if($booking_list->count())
<div class="booking-grid">
@foreach($booking_list as $booking)
<div class="booking-card">

<div class="card-top">
    <div>
        <div class="date-box">
            <div>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d') }}</div>
            <small>{{ \Carbon\Carbon::parse($booking->booking_date)->format('M Y') }}</small>
        </div>
        <strong>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</strong>
    </div>

    <span class="status-badge {{ $booking->status }}">
        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
    </span>
</div>

<div class="card-body">
    <div class="patient-row">
        <div class="patient-avatar">
            {{ strtoupper(substr($booking->patient_name,0,1)) }}
        </div>
        <div>
            <strong>{{ $booking->patient_name }}</strong><br>
            <small>{{ $booking->patient_email }}</small>
        </div>
    </div>

    <div class="info-chip">📞 {{ $booking->patient_phone }}</div>
    <div class="info-chip">👤 {{ $booking->age }} yrs</div><br>
    <div class="info-chip">📋 {{ $booking->cause }}</div>


   @if($booking->status === 'pending')
    {{-- PENDING: Accept, Reject, Reschedule --}}
    <div class="action-btns">
        <button class="btn-action btn-accept"
            onclick="updateStatus({{ $booking->id }}, 'accepted')">
            ✅ Accept
        </button>
        <button class="btn-action btn-reject"
            onclick="updateStatus({{ $booking->id }}, 'rejected')">
            ❌ Reject
        </button>
        <button class="btn-action btn-reschedule"
            onclick="openRescheduleModal({{ $booking->id }}, '{{ $booking->patient_name }}', '{{ $booking->booking_date }}', '{{ $booking->start_time }}')">
            📅 Reschedule
        </button>
    </div>

@elseif($booking->status === 'accepted')
    {{-- ACCEPTED: Reschedule, No Show, Reject, Completed --}}
    <div class="approved-msg" style="background: #ecfdf5; color: #047857;">
        ✅ Booking Accepted
        <br><br>
        <div class="action-btns">
            <button class="btn-action btn-reschedule"
                onclick="openRescheduleModal({{ $booking->id }}, '{{ $booking->patient_name }}', '{{ $booking->booking_date }}', '{{ $booking->start_time }}')">
                📅 Reschedule
            </button>
            <button class="btn-action btn-noshow"
                onclick="updateStatus({{ $booking->id }}, 'no_show')">
                🚫 No Show
            </button>
            <button class="btn-action btn-reject"
                onclick="updateStatus({{ $booking->id }}, 'rejected')">
                ❌ Reject
            </button>
            <button class="btn-action btn-completed"
                onclick="updateStatus({{ $booking->id }}, 'completed')">
                ✔️ Completed
            </button>
        </div>
    </div>

@elseif($booking->status === 'rejected')
    {{-- REJECTED: Accept, Reschedule --}}
    <div class="rejected-msg" style="background: #fef2f2; color: #b91c1c;">
        ❌ Booking Rejected
        <br><br>
        <div class="action-btns">
            <button class="btn-action btn-accept"
                onclick="updateStatus({{ $booking->id }}, 'accepted')">
                ✅ Accept
            </button>
            <button class="btn-action btn-reschedule"
                onclick="openRescheduleModal({{ $booking->id }}, '{{ $booking->patient_name }}', '{{ $booking->booking_date }}', '{{ $booking->start_time }}')">
                📅 Reschedule
            </button>
        </div>
    </div>

@elseif($booking->status === 'no_show')
    {{-- NO SHOW: Accept, Reschedule --}}
    <div class="rejected-msg" style="background: #f3f4f6; color: #4b5563;">
        🚫 Patient No Show
        <br><br>
        <div class="action-btns">
            <button class="btn-action btn-accept"
                onclick="updateStatus({{ $booking->id }}, 'accepted')">
                ✅ Accept
            </button>
            <button class="btn-action btn-reschedule"
                onclick="openRescheduleModal({{ $booking->id }}, '{{ $booking->patient_name }}', '{{ $booking->booking_date }}', '{{ $booking->start_time }}')">
                📅 Reschedule
            </button>
        </div>
    </div>

@elseif($booking->status === 'rescheduled')
    {{-- RESCHEDULED: Completed, No Show, Reject --}}
    <div class="approved-msg" style="background: #eff6ff; color: #1d4ed8;">
        📅 Booking Rescheduled
        <br><br>
        <div class="action-btns">
            <button class="btn-action btn-completed"
                onclick="updateStatus({{ $booking->id }}, 'completed')">
                ✔️ Completed
            </button>
            <button class="btn-action btn-noshow"
                onclick="updateStatus({{ $booking->id }}, 'no_show')">
                🚫 No Show
            </button>
            <button class="btn-action btn-reject"
                onclick="updateStatus({{ $booking->id }}, 'rejected')">
                ❌ Reject
            </button>
        </div>
    </div>

@elseif($booking->status === 'completed')
    {{-- COMPLETED: Show only message, no buttons --}}
    <div class="completed-msg" style="background: #d1fae5; color: #065f46;">
        ✔️ Appointment Completed
        <br>
        <small style="margin-top: 8px; display: block; opacity: 0.8;">
            This booking has been successfully completed
        </small>
    </div>

@elseif($booking->status === 'cancelled')
    {{-- CANCELLED: Accept, Reschedule --}}
    <div class="rejected-msg" style="background: #f3f4f6; color: #374151;">
        ⛔ Booking Cancelled
        <br><br>
        <div class="action-btns">
            <button class="btn-action btn-accept"
                onclick="updateStatus({{ $booking->id }}, 'accepted')">
                ✅ Accept
            </button>
            <button class="btn-action btn-reschedule"
                onclick="openRescheduleModal({{ $booking->id }}, '{{ $booking->patient_name }}', '{{ $booking->booking_date }}', '{{ $booking->start_time }}')">
                📅 Reschedule
            </button>
        </div>
    </div>

@elseif($booking->status === 'unverified')
    {{-- UNVERIFIED: Accept, Reject --}}
    <div class="rejected-msg" style="background: #fef3c7; color: #92400e;">
        ⚠️ Unverified Booking
        <br><br>
        <div class="action-btns">
            <button class="btn-action btn-accept"
                onclick="updateStatus({{ $booking->id }}, 'accepted')">
                ✅ Accept
            </button>
            <button class="btn-action btn-reject"
                onclick="updateStatus({{ $booking->id }}, 'rejected')">
                ❌ Reject
            </button>
        </div>
    </div>
@endif

</div>

</div>
@endforeach
</div>
@else
<div class="empty-state">
    <h3>📭 No bookings found</h3>
    <p>Try changing the filter or date.</p>
</div>
@endif

</div>

{{-- Reschedule Modal --}}
<div id="rescheduleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>📅 Reschedule Appointment</h2>
            <button class="close-btn" onclick="closeRescheduleModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="patient-info-box">
                <h4>Patient Information</h4>
                <p><strong>Name:</strong> <span id="modal-patient-name"></span></p>
                <p><strong>Current Date:</strong> <span id="modal-current-date"></span></p>
                <p><strong>Current Time:</strong> <span id="modal-current-time"></span></p>
            </div>

            <form id="rescheduleForm">
                <input type="hidden" id="booking-id" name="booking_id">

                <div class="form-group">
                    <label for="new-date">New Appointment Date *</label>
                    <input type="date"
                           id="new-date"
                           name="new_date"
                           required
                           min="{{ date('Y-m-d') }}">
                    <small>Select a new date for the appointment</small>
                </div>

                <div class="form-group">
                    <label for="new-time">New Appointment Time *</label>
                    <input type="time"
                           id="new-time"
                           name="new_time"
                           required>
                    <small>Select a new time for the appointment</small>
                </div>

                <div class="form-group">
                    <label for="reason">Reason for Rescheduling (Optional)</label>
                    <textarea id="reason"
                              name="reason"
                              placeholder="Enter the reason for rescheduling..."></textarea>
                    <small>This will be sent to the patient</small>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-cancel" onclick="closeRescheduleModal()">Cancel</button>
            <button type="button" class="btn-modal btn-submit" onclick="submitReschedule()">Confirm Reschedule</button>
        </div>
    </div>
</div>

<script>
// Update booking status
function updateStatus(id, status) {
    let confirmMessage = 'Confirm action?';

    if (status === 'no_show') {
        confirmMessage = 'Mark this patient as No Show?';
    } else if (status === 'rejected') {
        confirmMessage = 'Are you sure you want to reject this booking?';
    } else if (status === 'accepted') {
        confirmMessage = 'Are you sure you want to accept this booking?';
    } else if (status === 'completed') {
        confirmMessage = 'Mark this appointment as completed?';
    }

    if (!confirm(confirmMessage)) return;

    fetch(`/doctor/bookings/${id}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            location.reload();
        } else {
            alert(res.msg || 'Failed to update status');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred while updating status.');
    });
}

// Open reschedule modal
function openRescheduleModal(bookingId, patientName, bookingDate, startTime) {
    // Set booking ID
    document.getElementById('booking-id').value = bookingId;

    // Set patient info
    document.getElementById('modal-patient-name').textContent = patientName;
    document.getElementById('modal-current-date').textContent = formatDate(bookingDate);
    document.getElementById('modal-current-time').textContent = formatTime(startTime);

    // Reset form
    document.getElementById('rescheduleForm').reset();

    // Show modal
    document.getElementById('rescheduleModal').classList.add('show');

    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

// Close reschedule modal
function closeRescheduleModal() {
    document.getElementById('rescheduleModal').classList.remove('show');
    document.body.style.overflow = 'auto';
}

// Submit reschedule
function submitReschedule() {
    const bookingId = document.getElementById('booking-id').value;
    const newDate = document.getElementById('new-date').value;
    const newTime = document.getElementById('new-time').value;
    const reason = document.getElementById('reason').value;

    // Validate
    if (!newDate || !newTime) {
        alert('Please select both date and time');
        return;
    }

    // Confirm
    if (!confirm(`Reschedule appointment to ${newDate} at ${newTime}?`)) {
        return;
    }

    // Submit
    fetch(`/doctor/bookings/${bookingId}/reschedule`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            new_date: newDate,
            new_time: newTime,
            reason: reason
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert('Appointment rescheduled successfully!');
            location.reload();
        } else {
            alert(res.msg || 'Failed to reschedule appointment');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred while rescheduling.');
    });
}

// Format date helper
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Format time helper
function formatTime(timeString) {
    return timeString;
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('rescheduleModal');
    if (event.target === modal) {
        closeRescheduleModal();
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRescheduleModal();
    }
});
</script>

@endsection
