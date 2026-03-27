@extends('layouts.app1')

@section('content')
<div class="container-fluid py-4">

    {{-- ── HEADER ── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color:#1363C6;">📅 My Bookings Calendar</h4>
            <div class="text-muted small">{{ auth()->user()->name }} · {{ $currentMonth->format('F Y') }}</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('doctor.calendar', ['month' => $prevMonth]) }}"
               class="btn btn-outline-secondary btn-sm px-3">‹ Prev</a>
            <span class="fw-semibold px-2" style="min-width:130px;text-align:center;">
                {{ $currentMonth->format('F Y') }}
            </span>
            <a href="{{ route('doctor.calendar', ['month' => $nextMonth]) }}"
               class="btn btn-outline-secondary btn-sm px-3">Next ›</a>
            <a href="{{ route('doctor.calendar', ['month' => now()->format('Y-m')]) }}"
               class="btn btn-sm ms-2" style="background:#1363C6;color:#fff;">Today</a>
        </div>
    </div>

    {{-- ── STATS ROW ── --}}
    <div class="row g-3 mb-4">
        @php
            $flat = collect($allBookings);
        @endphp
        @foreach([
            ['pending',  '#f59e0b', 'Pending'],
            ['accepted', '#10b981', 'Accepted'],
            ['completed','#1363C6', 'Completed'],
        ] as [$st, $col, $lbl])
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-left:4px solid {{ $col }} !important;">
                <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted fw-semibold text-uppercase" style="font-size:.7rem;">{{ $lbl }}</div>
                        <div class="fs-4 fw-bold" style="color:{{ $col }};">
                            {{ $flat->where('status', $st)->count() }}
                        </div>
                    </div>
                    <div style="font-size:1.8rem;opacity:.2;">
                        {{ $st === 'pending' ? '🕐' : ($st === 'accepted' ? '✅' : '🏁') }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── STATUS LEGEND ── --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        @foreach([
            'pending'     => ['#f59e0b','Pending'],
            'accepted'    => ['#10b981','Accepted'],
            'completed'   => ['#1363C6','Completed'],
            'rejected'    => ['#ef4444','Rejected'],
            'cancelled'   => ['#6b7280','Cancelled'],
            'rescheduled' => ['#8b5cf6','Rescheduled'],
            'no_show'     => ['#f97316','No Show'],
        ] as $status => [$color, $label])
        <span class="badge rounded-pill px-3 py-2" style="background:{{ $color }}1a;color:{{ $color }};border:1px solid {{ $color }}40;font-size:0.75rem;">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $color }};margin-right:5px;"></span>{{ $label }}
        </span>
        @endforeach
    </div>

    {{-- ── CALENDAR GRID ── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            {{-- Day headers --}}
            <div class="row g-0 border-bottom" style="background:#1363C6;">
                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                <div class="col text-center text-white fw-semibold py-2" style="font-size:0.8rem;letter-spacing:.05em;">
                    {{ $day }}
                </div>
                @endforeach
            </div>

            {{-- Weeks --}}
            @foreach($weeks as $week)
            <div class="row g-0 border-bottom">
                @foreach($week as $dayData)
                @php
                    $isToday      = $dayData['date'] && $dayData['date']->isToday();
                    $isOtherMonth = $dayData['date'] && !$dayData['inMonth'];
                    $bookings     = $dayData['bookings'] ?? collect();
                    $maxShow      = 3;
                    $extra        = max(0, $bookings->count() - $maxShow);
                @endphp
                <div class="col border-end cal-cell {{ $isToday ? 'today-cell' : '' }} {{ $isOtherMonth ? 'other-month' : '' }}"
                     style="min-height:115px;padding:6px 7px;">

                    @if($dayData['date'])
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="day-num {{ $isToday ? 'today-badge' : '' }}">
                            {{ $dayData['date']->format('j') }}
                        </span>
                        @if($bookings->count() > 0)
                        <span class="badge rounded-pill" style="background:#1363C620;color:#1363C6;font-size:.65rem;">
                            {{ $bookings->count() }}
                        </span>
                        @endif
                    </div>

                    @foreach($bookings->take($maxShow) as $booking)
                    @php
                        $colors = [
                            'pending'     => ['#f59e0b','#fffbeb'],
                            'accepted'    => ['#10b981','#ecfdf5'],
                            'completed'   => ['#1363C6','#eff6ff'],
                            'rejected'    => ['#ef4444','#fef2f2'],
                            'cancelled'   => ['#6b7280','#f9fafb'],
                            'rescheduled' => ['#8b5cf6','#f5f3ff'],
                            'no_show'     => ['#f97316','#fff7ed'],
                        ];
                        [$tc, $bg] = $colors[$booking->status] ?? ['#64748b','#f8fafc'];
                    @endphp
                    <div class="booking-pill mb-1"
                         data-bs-toggle="modal"
                         data-bs-target="#bookingModal"
                         data-id="{{ $booking->id }}"
                         data-patient="{{ $booking->patient_name }}"
                         data-phone="{{ $booking->patient_phone }}"
                         data-date="{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}"
                         data-time="{{ $booking->start_time ? \Carbon\Carbon::parse($booking->start_time)->format('h:i A') : '—' }}"
                         data-status="{{ $booking->status }}"
                         data-cause="{{ $booking->cause ?? '—' }}"
                         data-token="{{ $booking->action_token }}"
                         style="background:{{ $bg }};border-left:3px solid {{ $tc }};border-radius:4px;padding:2px 6px;cursor:pointer;font-size:0.7rem;color:{{ $tc }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <span style="font-weight:600;">{{ $booking->start_time ? \Carbon\Carbon::parse($booking->start_time)->format('h:i A') : '' }}</span>
                        {{ Str::limit($booking->patient_name, 16) }}
                    </div>
                    @endforeach

                    @if($extra > 0)
                    <div class="text-muted" style="font-size:.68rem;padding-left:4px;cursor:pointer;"
                         onclick="loadDayBookings('{{ $dayData['date']->format('Y-m-d') }}', '{{ $dayData['date']->format('d M Y') }}')">
                        +{{ $extra }} more
                    </div>
                    @endif
                    @endif
                </div>
                @endforeach
            </div>
            @endforeach

        </div>
    </div>
</div>

{{-- ── BOOKING DETAIL MODAL ── --}}
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0" style="background:#f8fafc;">
                <div>
                    <h6 class="modal-title fw-bold mb-0" id="modalPatientName"></h6>
                    <div class="small text-muted" id="modalToken"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="detail-block">
                            <div class="detail-label">📅 Date</div>
                            <div class="detail-val" id="modalDate"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="detail-block">
                            <div class="detail-label">🕐 Time</div>
                            <div class="detail-val" id="modalTime"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="detail-block">
                            <div class="detail-label">📱 Phone</div>
                            <div class="detail-val" id="modalPhone"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="detail-block">
                            <div class="detail-label">Status</div>
                            <div id="modalStatus"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="detail-block">
                            <div class="detail-label">🩺 Cause</div>
                            <div class="detail-val" id="modalCause"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a href="{{ route('doctor.overall_bookings') }}" class="btn btn-sm" style="background:#1363C6;color:#fff;">
                    View All Bookings →
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ── DAY OVERFLOW MODAL ── --}}
<div class="modal fade" id="dayModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0" style="background:#1363C6;">
                <h6 class="modal-title text-white fw-bold" id="dayModalLabel">All Bookings</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-2" id="dayModalBody">
                <div class="text-center text-muted py-3">Loading…</div>
            </div>
        </div>
    </div>
</div>

<style>
.cal-cell { transition: background .15s; }
.cal-cell:hover { background: #f0f7ff; }
.other-month { background: #fafafa; }
.other-month .day-num { color: #c0c0c0; }
.today-cell { background: #eff6ff !important; }
.day-num { font-size:.82rem;font-weight:600;color:#374151;display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%; }
.today-badge { background:#1363C6;color:#fff !important; }
.booking-pill:hover { filter: brightness(.95); }
.detail-block { background:#f8fafc;border-radius:8px;padding:8px 10px; }
.detail-label { font-size:.7rem;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px; }
.detail-val { font-size:.85rem;font-weight:600;color:#1e293b; }
</style>

<script>
const statusColors = {
    pending:'#f59e0b', accepted:'#10b981', completed:'#1363C6',
    rejected:'#ef4444', cancelled:'#6b7280', rescheduled:'#8b5cf6', no_show:'#f97316'
};

document.getElementById('bookingModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    if (!btn || !btn.dataset.id) return;
    document.getElementById('modalPatientName').textContent = btn.dataset.patient;
    document.getElementById('modalToken').textContent       = 'Ref: ' + btn.dataset.token;
    document.getElementById('modalDate').textContent        = btn.dataset.date;
    document.getElementById('modalTime').textContent        = btn.dataset.time;
    document.getElementById('modalPhone').textContent       = btn.dataset.phone;
    document.getElementById('modalCause').textContent       = btn.dataset.cause;
    const st = btn.dataset.status;
    const color = statusColors[st] || '#64748b';
    document.getElementById('modalStatus').innerHTML =
        `<span class="badge" style="background:${color}20;color:${color};border:1px solid ${color}40;font-size:.78rem;padding:4px 10px;border-radius:20px;">${st.charAt(0).toUpperCase()+st.slice(1).replace('_',' ')}</span>`;
});

const allBookings = @json($allBookings);

function loadDayBookings(date, label) {
    document.getElementById('dayModalLabel').textContent = label;
    const modal = new bootstrap.Modal(document.getElementById('dayModal'));
    modal.show();
    const day  = allBookings.filter(b => b.booking_date === date);
    const body = document.getElementById('dayModalBody');
    if (!day.length) { body.innerHTML = '<div class="text-center text-muted py-3">No bookings</div>'; return; }
    body.innerHTML = day.map(b => {
        const c    = statusColors[b.status] || '#64748b';
        const time = b.start_time ? new Date('1970-01-01T'+b.start_time).toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'}) : '—';
        return `<div class="d-flex align-items-center gap-2 p-2 mb-1 rounded" style="background:${c}12;border-left:3px solid ${c};cursor:pointer;"
                     data-bs-toggle="modal" data-bs-target="#bookingModal"
                     data-id="${b.id}" data-patient="${b.patient_name}" data-phone="${b.patient_phone}"
                     data-date="${label}" data-time="${time}" data-status="${b.status}"
                     data-cause="${b.cause||'—'}" data-token="${b.action_token}">
                    <div>
                        <div style="font-size:.8rem;font-weight:700;color:${c};">${time}</div>
                        <div style="font-size:.78rem;font-weight:600;">${b.patient_name}</div>
                    </div>
                </div>`;
    }).join('');
}
</script>
@endsection