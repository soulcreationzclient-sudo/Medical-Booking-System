@extends('layouts.app2')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- ── SUMMARY CARDS ── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-white bg-success h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="font-size:2.5rem;">💰</div>
                    <div>
                        <div class="small opacity-75 fw-semibold text-uppercase">Total Income</div>
                        <div class="fs-3 fw-bold">₹{{ number_format($totalProfit, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-white bg-danger h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="font-size:2.5rem;">📉</div>
                    <div>
                        <div class="small opacity-75 fw-semibold text-uppercase">Total Expenses</div>
                        <div class="fs-3 fw-bold">₹{{ number_format($totalExpense, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-white h-100
                {{ $netBalance >= 0 ? 'bg-primary' : 'bg-dark' }}">
                <div class="card-body d-flex align-items-center gap-3">
                    <div style="font-size:2.5rem;">{{ $netBalance >= 0 ? '📈' : '⚠️' }}</div>
                    <div>
                        <div class="small opacity-75 fw-semibold text-uppercase">Net Balance</div>
                        <div class="fs-3 fw-bold">₹{{ number_format(abs($netBalance), 2) }}
                            <small class="fs-6">{{ $netBalance >= 0 ? 'Profit' : 'Loss' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- ── LEFT: LEDGER TABLE ── --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">📒 Financial Ledger</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addFinancialModal"
                                onclick="setFinancialType('profit')">+ Add Income</button>
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#addFinancialModal"
                                onclick="setFinancialType('expense')">+ Add Expense</button>
                    </div>
                </div>
                <div class="card-body p-0">

                    {{-- Filter tabs --}}
                    <div class="d-flex gap-2 p-3 border-bottom">
                        <button class="btn btn-sm btn-outline-secondary active" onclick="filterLedger('all', this)">All</button>
                        <button class="btn btn-sm btn-outline-success" onclick="filterLedger('profit', this)">Income</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="filterLedger('expense', this)">Expenses</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="ledgerTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                <tr data-type="{{ $entry->type }}">
                                    <td class="small text-muted">{{ $entry->entry_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $entry->type === 'profit' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $entry->type === 'profit' ? '↑ Income' : '↓ Expense' }}
                                        </span>
                                    </td>
                                    <td>{{ $entry->description }}</td>
                                    <td class="fw-bold {{ $entry->type === 'profit' ? 'text-success' : 'text-danger' }}">
                                        {{ $entry->type === 'profit' ? '+' : '-' }}₹{{ number_format($entry->amount, 2) }}
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteFinancial({{ $entry->id }}, this)">✕</button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No entries yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: MONTHLY BAR CHART ── --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0 fw-bold">📊 Monthly Overview (Last 6 Months)</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" style="max-height:280px;"></canvas>
                </div>
            </div>

            {{-- QUICK ADD EXPENSE CARD --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white py-2">
                    <h6 class="mb-0 fw-bold">Quick Add</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('hospital_admin.financials.store') }}">
                        @csrf
                        <input type="hidden" name="type" id="quickType" value="expense">
                        <div class="mb-2">
                            <div class="btn-group w-100 mb-2" role="group">
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="setQuickType('profit')">+ Income</button>
                                <button type="button" class="btn btn-outline-danger btn-sm active" onclick="setQuickType('expense')">+ Expense</button>
                            </div>
                        </div>
                        <input type="text" name="description" class="form-control mb-2" placeholder="Description" required>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <input type="number" name="amount" class="form-control" placeholder="₹ Amount" step="0.01" min="0.01" required>
                            </div>
                            <div class="col-6">
                                <input type="date" name="entry_date" class="form-control" value="{{ now()->toDateString() }}" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-sm fw-semibold">Add Entry</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── ADD FINANCIAL ENTRY MODAL ── --}}
<div class="modal fade" id="addFinancialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('hospital_admin.financials.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white" id="financialModalHeader">
                    <h5 class="modal-title" id="financialModalTitle">Add Entry</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" id="financialType" value="profit">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" required placeholder="What is this entry for?">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                            <input type="date" name="entry_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// ── MONTHLY CHART ─────────────────────────────────────
@php
    $months = $monthlyData->pluck('month')->unique()->sort()->values();
    $profitByMonth  = $monthlyData->where('type','profit')->pluck('total','month');
    $expenseByMonth = $monthlyData->where('type','expense')->pluck('total','month');
    $labels         = $months->map(fn($m) => \Carbon\Carbon::parse($m.'-01')->format('M Y'))->values();
    $profitArr      = $months->map(fn($m) => (float)($profitByMonth[$m]  ?? 0))->values();
    $expenseArr     = $months->map(fn($m) => (float)($expenseByMonth[$m] ?? 0))->values();
@endphp

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: @json($labels),
        datasets: [
            {
                label: 'Income',
                data: @json($profitArr),
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderColor: '#16a34a',
                borderWidth: 1,
                borderRadius: 4,
            },
            {
                label: 'Expense',
                data: @json($expenseArr),
                backgroundColor: 'rgba(239,68,68,0.7)',
                borderColor: '#dc2626',
                borderWidth: 1,
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: {
                callbacks: { label: ctx => ` ₹${ctx.parsed.y.toLocaleString('en-IN')}` }
            }
        },
        scales: {
            y: {
                ticks: { callback: val => '₹' + val.toLocaleString('en-IN') }
            }
        }
    }
});

// ── FILTER LEDGER ─────────────────────────────────────
function filterLedger(type, btn) {
    document.querySelectorAll('#ledgerTable tbody tr[data-type]').forEach(row => {
        row.style.display = (type === 'all' || row.dataset.type === type) ? '' : 'none';
    });
    document.querySelectorAll('.btn-group .btn, button[onclick*="filterLedger"]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

// ── MODAL TYPE SETTER ─────────────────────────────────
function setFinancialType(type) {
    document.getElementById('financialType').value = type;
    const header = document.getElementById('financialModalHeader');
    const title  = document.getElementById('financialModalTitle');
    if (type === 'profit') {
        header.className = 'modal-header bg-success text-white';
        title.textContent = '+ Add Income';
    } else {
        header.className = 'modal-header bg-danger text-white';
        title.textContent = '+ Add Expense';
    }
}

// ── QUICK ADD TYPE ────────────────────────────────────
function setQuickType(type) {
    document.getElementById('quickType').value = type;
    document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
}

// ── DELETE ENTRY ──────────────────────────────────────
function deleteFinancial(id, btn) {
    if (!confirm('Delete this entry?')) return;
    fetch(`/hospital_admin/financials/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => { if (data.success) btn.closest('tr').remove(); });
}
</script>
@endsection