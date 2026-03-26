@extends('layouts.app1')
@section('title', 'Booking Calendar')

@section('content')
<style>
/* ── CALENDAR LAYOUT ── */
.cal-wrap {
    padding: 24px;
    background: #f4f7fb;
    min-height: 100vh;
}
.cal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}
.cal-title {
    font-size: 1.6rem;
    font-weight: 800;
    color: #1e3a8a;
}
.cal-nav {
    display: flex;
    align-items: center;
    gap: 12px;
}
.cal-nav button {
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 15px;
    font-weight: 600;
    color: #1363C6;
    cursor: pointer;
    transition: all 0.15s;
}
.cal-nav button:hover {
    background: #1363C6;
    color: #fff;
    border-color: #1363C6;
}
.cal-month-label {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1e293b;
    min-width: 160px;
    text-align: center;
}
.cal-today-btn {
    background: #1363C6 !important;
    color: #fff !important;
    border-color: #1363C6 !important;
    font-size: 13px !important;
    padding: 5px 14px !important;
}

/* ── GRID ── */
.cal-grid {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(19,99,198,0.08);
    overflow: hidden;
}
.cal-days-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: #1363C6;
}
.cal-days-header div {
    padding: 10px 0;
    text-align: center;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.cal-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border-left: 1px solid #e2e8f0;
    border-top: 1px solid #e2e8f0;
}
.cal-cell {
    min-height: 110px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    padding: 6px;
    cursor: pointer;
    transition: background 0.15s;
    position: relative;
}
.cal-cell:hover { background: #f0f7ff; }
.cal-cell.today { background: #eff6ff; }
.cal-cell.today .cal-date { color: #1363C6; font-weight: 800; }
.cal-cell.other-month { background: #fafafa; }
.cal-cell.other-month .cal-date { color: #cbd5e1; }
.cal-cell.has-bookings { background: #f0fdf4; }
.cal-cell.has-bookings:hover { background: #dcfce7; }
.cal-cell.selected { background: #eff6ff; outline: 2px solid #1363C6; }

.cal-date {
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 4px;
    display: block;
}
.cal-dot-row {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
    margin-top: 2px;
}
.cal-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #1363C6;
    display: inline-block;
}
.cal-count-badge {
    display: inline-block;
    background: #1363C6;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    border-radius: 20px;
    padding: 1px 7px;
    margin-top: 3px;
}
.cal-pill {
    display: block;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}
.cal-pill.pending   { background: #fef9c3; color: #854d0e; }
.cal-pill.accepted  { background: #dcfce7; color: #166534; }
.cal-pill.completed { background: #eff6ff; color: #1d4ed8; }
.cal-pill.rejected, .cal-pill.cancelled { background: #fee2e2; color: #991b1b; }
.cal-pill.rescheduled { background: #ede9fe; color: #5b21b6; }
.cal-pill.no_show   { background: #f3f4f6; color: #4b5563; }

/* ── SIDE PANEL ── */
.cal-side {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(19,99,198,0.08);
    overflow: hidden;
}
.cal-side-header {
    background: #1363C6;
    color: #fff;
    padding: 16px 20px;
    font-size: 15px;
    font-weight: 700;
}
.cal-side-body { padding: 16px; }

.booking-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 10px;
    transition: border-color 0.15s;
}
.booking-card:hover { border-color: #1363C6; }
.booking-card:last-child { margin-bottom: 0; }

.booking-name {
    font-weight: 700;
    font-size: 14px;
    color: #1e293b;
    margin-bottom: 3px;
}
.booking-meta {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 6px;
}
.status-pill {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 10px;
    border-radius: 20px;
}
.status-pill.pending    { background: #fef9c3; color: #854d0e; }
.status-pill.accepted   { background: #dcfce7; color: #166534; }
.status-pill.completed  { background: #eff6ff; color: #1d4ed8; }
.status-pill.rejected   { background: #fee2e2; color: #991b1b; }
.status-pill.cancelled  { background: #fee2e2; color: #991b1b; }
.status-pill.rescheduled { background: #ede9fe; color: #5b21b6; }
.status-pill.no_show    { background: #f3f4f6; color: #4b5563; }

.empty-day {
    text-align: center;
    padding: 40px 20px;
    color: #94a3b8;
    font-size: 14px;
}
</style>

<div class="cal-wrap">

    {{-- HEADER --}}
    <div class="cal-header">
        <div>
            <div class="cal-title">Booking Calendar</div>
            <div style="font-size:13px;color:#64748b;margin-top:2px;">
                @can('hospital_admin') All doctors &mdash; @endcan
                Click any date to view bookings
            </div>
        </div>
        <div class="cal-nav">
            <button onclick="changeMonth(-1)">&#8592;</button>
            <div class="cal-month-label" id="monthLabel"></div>
            <button onclick="changeMonth(1)">&#8594;</button>
            <button class="cal-today-btn" onclick="goToday()">Today</button>
        </div>
    </div>

    <div class="row g-3">
        {{-- CALENDAR --}}
        <div class="col-lg-8">
            <div class="cal-grid">
                <div class="cal-days-header">
                    <div>Sun</div><div>Mon</div><div>Tue</div>
                    <div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                </div>
                <div class="cal-body" id="calBody"></div>
            </div>
        </div>

        {{-- SIDE PANEL --}}
        <div class="col-lg-4">
            <div class="cal-side">
                <div class="cal-side-header" id="sidePanelTitle">Select a date</div>
                <div class="cal-side-body" id="sidePanelBody">
                    <div class="empty-day">Click any date on the calendar to see bookings for that day.</div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// ── INITIAL DATA FROM PHP + AJAX URL ──────────────────────────
const BOOKINGS_AJAX_URL = "{{ $calendarAjaxRoute ?? '' }}";
const initialBookings   = @json($bookings ?? []);

// ── STATE ─────────────────────────────────────────────────────
const today      = new Date();
let   curYear    = today.getFullYear();
let   curMonth   = today.getMonth(); // 0-indexed
let   selectedDate  = null;

// Pre-populate from server-rendered data
let bookingsByDate = {};
initialBookings.forEach(b => {
    const d = b.booking_date.substring(0, 10);
    if (!bookingsByDate[d]) bookingsByDate[d] = [];
    bookingsByDate[d].push(b);
});

// ── FETCH BOOKINGS FOR A GIVEN MONTH ──────────────────────────
async function fetchBookings(year, month) {
    const url = `${BOOKINGS_AJAX_URL}?year=${year}&month=${month}`;
    try {
        const res = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) throw new Error('Network response was not ok');
        const data = await res.json();
        bookingsByDate = {};
        data.forEach(b => {
            const d = b.booking_date.substring(0, 10);
            if (!bookingsByDate[d]) bookingsByDate[d] = [];
            bookingsByDate[d].push(b);
        });
    } catch (err) {
        console.error('Failed to fetch bookings:', err);
        bookingsByDate = {};
    }
}
const MONTH_NAMES = ['January','February','March','April','May','June',
                     'July','August','September','October','November','December'];

// ── RENDER CALENDAR ───────────────────────────────────────────
function renderCalendar() {
    document.getElementById('monthLabel').textContent =
        MONTH_NAMES[curMonth] + ' ' + curYear;

    const body    = document.getElementById('calBody');
    body.innerHTML = '';

    const firstDay = new Date(curYear, curMonth, 1).getDay(); // 0=Sun
    const daysInMonth  = new Date(curYear, curMonth + 1, 0).getDate();
    const daysInPrev   = new Date(curYear, curMonth, 0).getDate();

    const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;

    for (let i = 0; i < totalCells; i++) {
        const cell = document.createElement('div');
        cell.className = 'cal-cell';

        let dayNum, dateObj, isOtherMonth = false;

        if (i < firstDay) {
            // Previous month
            dayNum = daysInPrev - firstDay + i + 1;
            dateObj = new Date(curYear, curMonth - 1, dayNum);
            isOtherMonth = true;
            cell.classList.add('other-month');
        } else if (i >= firstDay + daysInMonth) {
            // Next month
            dayNum = i - firstDay - daysInMonth + 1;
            dateObj = new Date(curYear, curMonth + 1, dayNum);
            isOtherMonth = true;
            cell.classList.add('other-month');
        } else {
            dayNum = i - firstDay + 1;
            dateObj = new Date(curYear, curMonth, dayNum);
        }

        const dateStr = formatDate(dateObj);

        // Today highlight
        if (formatDate(today) === dateStr) cell.classList.add('today');

        // Selected
        if (selectedDate === dateStr) cell.classList.add('selected');

        // Bookings for this date
        const dayBookings = bookingsByDate[dateStr] || [];
        if (dayBookings.length > 0) cell.classList.add('has-bookings');

        // Date number
        const dateSpan = document.createElement('span');
        dateSpan.className = 'cal-date';
        dateSpan.textContent = dayNum;
        cell.appendChild(dateSpan);

        // Show up to 2 booking pills, then "+N more"
        if (dayBookings.length > 0) {
            const showMax = 2;
            dayBookings.slice(0, showMax).forEach(b => {
                const pill = document.createElement('span');
                pill.className = 'cal-pill ' + b.status;
                pill.textContent = b.patient_name;
                cell.appendChild(pill);
            });
            if (dayBookings.length > showMax) {
                const more = document.createElement('span');
                more.className = 'cal-count-badge';
                more.textContent = '+' + (dayBookings.length - showMax) + ' more';
                cell.appendChild(more);
            }
        }

        // Click handler
        cell.addEventListener('click', () => selectDate(dateStr, dateObj));

        body.appendChild(cell);
    }
}

// ── SELECT DATE ───────────────────────────────────────────────
function selectDate(dateStr, dateObj) {
    selectedDate = dateStr;
    renderCalendar(); // re-render to show selected highlight

    const bookings = bookingsByDate[dateStr] || [];
    const label    = dateObj.toLocaleDateString('en-GB', { weekday:'long', day:'numeric', month:'long', year:'numeric' });

    document.getElementById('sidePanelTitle').textContent =
        label + (bookings.length ? ' — ' + bookings.length + ' booking' + (bookings.length > 1 ? 's' : '') : '');

    const body = document.getElementById('sidePanelBody');

    if (bookings.length === 0) {
        body.innerHTML = '<div class="empty-day">No bookings on this day.</div>';
        return;
    }

    body.innerHTML = '';
    bookings.forEach(b => {
        const card = document.createElement('div');
        card.className = 'booking-card';

        const time = b.start_time
            ? new Date('1970-01-01T' + b.start_time).toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit' })
            : '—';

        card.innerHTML = `
            <div class="booking-name">${escHtml(b.patient_name)}</div>
            <div class="booking-meta">
                ${time}
                ${b.patient_phone ? ' &nbsp;·&nbsp; ' + escHtml(b.patient_phone) : ''}
                ${b.age ? ' &nbsp;·&nbsp; ' + b.age + ' yrs' : ''}
                ${b.doctor_name ? '<br>Dr. ' + escHtml(b.doctor_name) : ''}
                ${b.cause ? '<br><span style="color:#94a3b8">' + escHtml(b.cause) + '</span>' : ''}
            </div>
            <span class="status-pill ${b.status}">${ucFirst(b.status.replace('_',' '))}</span>
            ${b.action_token ? '<span style="font-family:monospace;font-size:11px;color:#94a3b8;margin-left:8px;">' + escHtml(b.action_token) + '</span>' : ''}
        `;
        body.appendChild(card);
    });
}

// ── NAVIGATION ────────────────────────────────────────────────
async function changeMonth(dir) {
    curMonth += dir;
    if (curMonth > 11) { curMonth = 0;  curYear++; }
    if (curMonth < 0)  { curMonth = 11; curYear--; }

    document.getElementById('sidePanelTitle').textContent = 'Loading…';
    document.getElementById('sidePanelBody').innerHTML =
        '<div class="empty-day">Fetching bookings…</div>';
    selectedDate = null;

    await fetchBookings(curYear, curMonth + 1);
    renderCalendar();

    document.getElementById('sidePanelTitle').textContent = 'Select a date';
    document.getElementById('sidePanelBody').innerHTML =
        '<div class="empty-day">Click any date on the calendar to see bookings for that day.</div>';
}

async function goToday() {
    curYear  = today.getFullYear();
    curMonth = today.getMonth();
    await fetchBookings(curYear, curMonth + 1);
    selectedDate = formatDate(today);
    renderCalendar();
    selectDate(selectedDate, today);
}

// ── HELPERS ───────────────────────────────────────────────────
function formatDate(d) {
    const y  = d.getFullYear();
    const m  = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${dd}`;
}
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function ucFirst(s) {
    return s.charAt(0).toUpperCase() + s.slice(1);
}

// ── INIT ──────────────────────────────────────────────────────
renderCalendar();
const todayStr = formatDate(today);
if (bookingsByDate[todayStr]) {
    selectedDate = todayStr;
    renderCalendar();
    selectDate(todayStr, today);
}
</script>

@endsection