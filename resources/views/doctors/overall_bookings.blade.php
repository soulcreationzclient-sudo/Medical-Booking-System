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


<!-- Add booking statistics cards here -->


{{--  📊 COMPACT KPI STATISTICS BAR --}}
<style>
.kpi-stats-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin: 0 0 18px 0;
    padding: 0;
    max-width: 1150px;
    width: 100%;
}
.kpi-chip {
    display: flex;
    align-items: center;
    height: 38px;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    padding: 0 18px 0 14px;
    font-size: 0.97rem;
    font-weight: 500;
    min-width: 144px;
    box-shadow: none;
    line-height: 1.2;
    gap: 9px;
    margin-bottom: 0;
}
.kpi-chip-label {
    font-size: 0.91em;
    color: #475569;
    margin-right: 6px;
    font-weight: 500;
    letter-spacing: .01em;
}
.kpi-chip-count {
    font-size: 1.08em;
    font-weight: 700;
    color: #1e293b;
    margin-left: auto;
    letter-spacing: .02em;
}
.kpi-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 6px;
}
.kpi-dot--gray { background: #cbd5e1; }
.kpi-dot--orange { background: #f59e42; }
.kpi-dot--green { background: #22c55e; }
.kpi-dot--blue { background: #3b82f6; }
.kpi-dot--red { background: #ef4444; }
@media (max-width: 860px) {
    .kpi-stats-bar {
        gap: 9px;
        max-width: 100%;
    }
    .kpi-chip {
        min-width: 136px;
        font-size: .96rem;
        padding: 0 13px 0 11px;
    }
}
@media (max-width: 600px) {
    .kpi-stats-bar {
        gap: 7px;
        margin-bottom: 14px;
    }
    .kpi-chip {
        min-width: 115px;
        font-size: .95rem;
        padding: 0 8px 0 8px;
    }
}
</style>
<div class="kpi-stats-bar">
    <div class="kpi-chip">
        <span class="kpi-dot kpi-dot--gray"></span>
        <span class="kpi-chip-label">Total</span>
        <span class="kpi-chip-count">{{ $booking_list->count() }}</span>
    </div>
    <div class="kpi-chip">
        <span class="kpi-dot kpi-dot--orange"></span>
        <span class="kpi-chip-label">Pending</span>
        <span class="kpi-chip-count">{{ $booking_list->where('status','pending')->count() }}</span>
    </div>
    <div class="kpi-chip">
        <span class="kpi-dot kpi-dot--green"></span>
        <span class="kpi-chip-label">Accepted</span>
        <span class="kpi-chip-count">{{ $booking_list->where('status','accepted')->count() }}</span>
    </div>
    <div class="kpi-chip">
        <span class="kpi-dot kpi-dot--blue"></span>
        <span class="kpi-chip-label">Completed</span>
        <span class="kpi-chip-count">{{ $booking_list->where('status','completed')->count() }}</span>
    </div>
    <div class="kpi-chip">
        <span class="kpi-dot kpi-dot--red"></span>
        <span class="kpi-chip-label">Rejected / No Show</span>
        <span class="kpi-chip-count">{{ $booking_list->whereIn('status', ['rejected', 'no_show'])->count() }}</span>
    </div>
</div>

{{--  FILTER TABS + SEARCH  --}}
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
<style>
/* --- SaaS/Stripe-like Compact Dashboard Table --- */
.saas-table-wrap {
  width: 100%;
  max-width: 1150px;
  margin: 0 auto 36px auto;
  overflow-x: auto;
  background: transparent;
}
.saas-table {
  width: 100%;
  min-width: 900px;
  border-collapse: separate;
  border-spacing: 0;
  background: #fff;
  font-size: 0.98rem;
  box-shadow: 0 2px 10px 0 rgba(30,58,138,.02), 0 .5px 1px #64748b10;
}
.saas-table th, .saas-table td {
  padding: 0.38rem 0.75rem;
  vertical-align: middle;
  white-space: nowrap;
  border: none;
}
.saas-table th {
  position: sticky;
  top: 0;
  z-index: 11;
  background: #fff;
  font-weight: 600;
  font-size: 0.97rem;
  color: #263347;
  border-bottom: 1.5px solid #e5e7eb;
  letter-spacing: 0.01em;
  text-align: left;
}
.saas-table tbody tr {
  height: 56px;
  border-bottom: 1px solid #f3f4f6;
  transition: background 0.15s;
  cursor: pointer;
}
.saas-table tbody tr:hover {
  background: #f8fafc;
}
.saas-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #e9effb;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #2960a8;
  font-weight: 700;
  font-size: 1.10rem;
  margin-right: 12px;
  flex-shrink: 0;
  user-select: none;
  letter-spacing: 1px;
}
.saas-patient-stack {
  display: flex;
  align-items: center;
  gap: 0.66rem;
}
.saas-patient-details {
  display: flex;
  flex-direction: column;
}
.saas-name-bold {
  font-weight: 600;
  font-size: 1.01rem;
  color: #111927;
}
.saas-email-muted {
  font-size: 0.88rem;
  color: #7c8899;
  font-weight: 400;
  margin-top: 1px;
  letter-spacing: 0.01em;
}
.saas-badge {
  border-radius: 9999px;
  padding: 0.14em 0.95em;
  font-size: 0.93rem;
  font-weight: 500;
  line-height: 1.25;
  display: inline-block;
  text-align: center;
  letter-spacing: 0.005em;
  white-space: nowrap;
  border: none;
  min-width: 78px;
  text-transform: capitalize;
  box-shadow: none;
}
.saas-badge.pending    { background: #fdf6d7; color: #b8861b; }
.saas-badge.accepted   { background: #e6fbe9; color: #1a9250;}
.saas-badge.completed  { background: #e5eeff; color: #3261cb;}
.saas-badge.rejected   { background: #fee2e2; color: #cb2d2b;}
.saas-badge.no_show    { background: #f0eafd; color: #845afd;}
.saas-badge.cancelled  { background: #f1f5f9; color: #757575;}
.saas-badge.rescheduled{ background: #eaf0fc; color: #3b4295;}
.saas-badge.unverified { background: #fdf6d7; color: #b8861b;}
.saas-actions-cell {
  position: relative;
  min-width: 48px;
  text-align: right;
}
/* Dropdown actions - visible always when row's actions column is "active" or hovered. Otherwise, only the 3 dots shown. */
.saas-dropdown-actions-row {
  display: flex;
  gap: 4px;
  justify-content: flex-end;
  align-items: center;
}

.saas-dropdown-btn {
  background: #f5f8fa;
  color: #295dc2;
  border: none;
  border-radius: 6px;
  padding: 4px 11px;
  font-size: 0.94rem;
  font-weight: 500;
  cursor: pointer;
  transition: background .13s, color .13s;
  display: inline-flex;
  align-items: center;
  gap: 0.38em;
  outline: none;
}
.saas-dropdown-btn.accept    { background: #e6fbe9; color: #16a34a; }
.saas-dropdown-btn.reject    { background: #fee2e2; color: #cb2d2b; }
.saas-dropdown-btn.resched   { background: #eaf0fc; color: #3b4295; }
.saas-dropdown-btn.noshow    { background: #f0eafd; color: #845afd;}
.saas-dropdown-btn.complete  { background: #def6e3; color: #1a9250;}
.saas-dropdown-btn:disabled,
.saas-dropdown-btn[aria-disabled="true"] { opacity: 0.47; pointer-events: none; }

.saas-actions-cell .saas-three-dot-btn,
.saas-actions-cell .saas-dropdown-btns-wrap {
  display: none;
}

.saas-actions-cell.active .saas-dropdown-btns-wrap,
.saas-actions-cell:focus-within .saas-dropdown-btns-wrap,
.saas-actions-cell.show-dropdown .saas-dropdown-btns-wrap,
.saas-actions-cell:hover .saas-dropdown-btns-wrap {
  display: flex !important;
}

.saas-actions-cell.active .saas-three-dot-btn,
.saas-actions-cell:focus-within .saas-three-dot-btn,
.saas-actions-cell.show-dropdown .saas-three-dot-btn,
.saas-actions-cell:hover .saas-three-dot-btn {
  display: none !important;
}

.saas-actions-cell .saas-three-dot-btn {
  background: none;
  border: none;
  outline: none;
  padding: 8px 6px;
  font-size: 1.43rem;
  color: #788195;
  cursor: pointer;
  border-radius: 6px;
  transition: background .14s;
  vertical-align: middle;
  display: inline-block;
}
.saas-actions-cell .saas-three-dot-btn:hover {
  background: #f1f5f9;
  color: #293859;
}
.saas-actions-cell:not(.active):not(:hover):not(.show-dropdown):not(:focus-within) .saas-three-dot-btn {
  display: inline-block;
}

/* Actions column left-aligned, never changes on hover */
.saas-table th:last-child,
.saas-table td.saas-actions-cell,
.saas-table td:last-child {
  text-align: left !important;
}

/* Prevent shift on hover for actions column cell */
.saas-actions-cell {
  text-align: left !important;
}

@media (max-width: 940px) {
  .saas-table { font-size: 0.95rem; min-width: 680px; }
}
@media (max-width: 600px) {
  .saas-table-wrap { max-width: 100vw; }
  .saas-table { font-size: 0.93rem; min-width: 540px;}
  .saas-table th, .saas-table td {padding: .36rem 0.48rem;}
}
</style>

<div class="saas-table-wrap">
  <table class="saas-table">
    <thead>
      <tr>
        <th style="min-width:170px;">Patient</th>
        <th>Date</th>
        <th>Time</th>
        <th>Phone</th>
        <th>Age</th>
        <th>Symptoms</th>
        <th>Status</th>
        <th style="min-width:68px;text-align:left;">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($booking_list as $booking)
      @php
        $statusClass = match($booking->status) {
          'pending'    => 'pending',
          'accepted'   => 'accepted',
          'completed'  => 'completed',
          'rejected'   => 'rejected',
          'no_show'    => 'no_show',
          'cancelled'  => 'cancelled',
          'rescheduled'=> 'rescheduled',
          'unverified' => 'unverified',
          default      => 'pending'
        };
        $nameInitial = strtoupper(substr($booking->patient_name,0,1));
        // Status
        $status = $booking->status;
      @endphp
      <tr>
        {{-- Patient --}}
        <td>
          <div class="saas-patient-stack">
            <div class="saas-avatar">{{ $nameInitial }}</div>
            <div class="saas-patient-details">
              <span class="saas-name-bold">{{ $booking->patient_name }}</span>
              <span class="saas-email-muted">{{ $booking->patient_email }}</span>
            </div>
          </div>
        </td>
        {{-- Date --}}
        <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d') }}</td>
        {{-- Time --}}
        <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}</td>
        {{-- Phone --}}
        <td>{{ $booking->patient_phone }}</td>
        {{-- Age --}}
        <td>{{ $booking->age }} yrs</td>
        {{-- Symptoms --}}
        <td>{{ $booking->cause }}</td>
        {{-- Status --}}
        <td>
          <span class="saas-badge {{ $statusClass }}">
            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
          </span>
        </td>
        {{-- Actions (Always show compact dropdown buttons per requirements) --}}
        <td class="saas-actions-cell" tabindex="0">
          <div class="saas-dropdown-btns-wrap saas-dropdown-actions-row">
            @if(in_array($status, ['pending','rejected','no_show','cancelled','unverified']))
              <button class="saas-dropdown-btn accept" 
                title="Accept"
                onclick="updateStatus({{ $booking->id }}, 'accepted')">
                &#10003; Accept
              </button>
            @endif
            @if(in_array($status, ['pending','accepted','rescheduled','unverified']))
              <button class="saas-dropdown-btn reject" 
                title="Reject"
                onclick="updateStatus({{ $booking->id }}, 'rejected')">
                &#10005; Reject
              </button>
            @endif
            @if(in_array($status, ['pending','accepted','rejected','no_show','cancelled']))
              <button class="saas-dropdown-btn resched" 
                title="Reschedule"
                onclick="openRescheduleModal(
                  {{ $booking->id }}, 
                  '{{ addslashes($booking->patient_name) }}',
                  '{{ $booking->booking_date }}',
                  '{{ $booking->start_time }}')">
                &#8635; Reschedule
              </button>
            @endif
            @if(in_array($status, ['accepted','rescheduled']))
              <button class="saas-dropdown-btn noshow" 
                title="Mark No Show"
                onclick="updateStatus({{ $booking->id }}, 'no_show')">
                &#9888; No Show
              </button>
              <button class="saas-dropdown-btn complete"
                title="Mark Complete"
                onclick="updateStatus({{ $booking->id }}, 'completed')">
                &#10003; Complete
              </button>
            @endif
            @if($status === 'completed')
              <button class="saas-dropdown-btn complete" disabled aria-disabled="true" title="Completed" style="color:#58b670;background:#eafbee;">
                &#10003; Completed
              </button>
            @endif
          </div>
          <button class="saas-three-dot-btn" tabindex="-1" aria-label="Actions">&#x22EF;</button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@else
<div class="empty-state" style="text-align:center;margin:60px 0 0;">
  <h3 style="font-size:1.18rem;color:#64748b;font-weight:600;">📭 No bookings found</h3>
  <p style="font-size:0.97rem;color:#9ca3af;">Try changing the filter or date.</p>
</div>
@endif

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
