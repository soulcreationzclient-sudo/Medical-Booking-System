@extends('layouts.app2')

@section('content')
<div class="container-fluid py-4">

    {{-- ── FLASH MESSAGES ── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">

        {{-- ── LEFT: TABLE + ADD FORM ── --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">💊 Medicines</h5>
                    <button class="btn btn-light btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                        + Add Medicine
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="medicinesTable">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Unit</th>
                                    <th>Price (₹)</th>
                                    <th>In Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($medicines as $i => $med)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-semibold">{{ $med->name }}</td>
                                    <td>{{ $med->unit }}</td>
                                    <td>₹{{ number_format($med->price, 2) }}</td>
                                    <td>
                                        @if($med->stock <= 5)
                                            <span class="badge bg-danger">{{ $med->stock }} ⚠️</span>
                                        @elseif($med->stock <= 20)
                                            <span class="badge bg-warning text-dark">{{ $med->stock }}</span>
                                        @else
                                            <span class="badge bg-success">{{ $med->stock }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1"
                                            onclick="openEditModal({{ $med->id }}, '{{ addslashes($med->name) }}', '{{ $med->unit }}', {{ $med->price }}, {{ $med->stock }}, '{{ addslashes($med->description ?? '') }}')">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteMedicine({{ $med->id }}, this)">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No medicines added yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: PIE CHART ── --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0 fw-bold">📊 Stock Distribution</h5>
                </div>
                <div class="card-body d-flex flex-column align-items-center">
                    @if($medicines->count())
                        <canvas id="stockPieChart" style="max-height:320px;"></canvas>
                        <p class="text-muted mt-3 small text-center">Each slice represents in-stock quantity per medicine</p>
                    @else
                        <p class="text-muted text-center mt-5">Add medicines to see the stock chart.</p>
                    @endif
                </div>
            </div>

            {{-- ── LOW STOCK ALERTS ── --}}
            @php $lowStock = $medicines->where('stock', '<=', 5); @endphp
            @if($lowStock->count())
            <div class="card shadow-sm border-0 mt-3 border-danger">
                <div class="card-header bg-danger text-white py-2">
                    ⚠️ Low Stock Alerts
                </div>
                <div class="card-body p-2">
                    @foreach($lowStock as $med)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-1 px-2">
                            <span class="fw-semibold">{{ $med->name }}</span>
                            <span class="badge bg-danger">{{ $med->stock }} left</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── ADD MEDICINE MODAL ── --}}
<div class="modal fade" id="addMedicineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('hospital_admin.medicines.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Medicine</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                            <select name="unit" class="form-select" required>
                                <option value="tablet">Tablet</option>
                                <option value="capsule">Capsule</option>
                                <option value="syrup (ml)">Syrup (ml)</option>
                                <option value="injection (ml)">Injection (ml)</option>
                                <option value="sachet">Sachet</option>
                                <option value="tube">Tube</option>
                                <option value="strip">Strip</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Price per unit (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label fw-semibold">Initial Stock <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description (optional)</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Medicine</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── EDIT MEDICINE MODAL ── --}}
<div class="modal fade" id="editMedicineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editMedicineForm">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Medicine</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Unit</label>
                            <input type="text" name="unit" id="edit_unit" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Price (₹)</label>
                            <input type="number" name="price" id="edit_price" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label fw-semibold">Stock</label>
                        <input type="number" name="stock" id="edit_stock" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ── PIE CHART ──────────────────────────────────────────────────
@if($medicines->count())
const stockData = @json($stockData);
const labels  = stockData.map(m => m.name);
const data    = stockData.map(m => m.stock);
const colors  = [
    '#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6',
    '#ec4899','#06b6d4','#84cc16','#f97316','#a78bfa',
    '#14b8a6','#fb923c','#60a5fa','#34d399','#fbbf24'
];

new Chart(document.getElementById('stockPieChart'), {
    type: 'pie',
    data: {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: colors.slice(0, labels.length),
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 12, font: { size: 12 } } },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.parsed} units`
                }
            }
        }
    }
});
@endif

// ── EDIT MODAL ─────────────────────────────────────────────────
function openEditModal(id, name, unit, price, stock, desc) {
    document.getElementById('editMedicineForm').action = `/hospital_admin/medicines/${id}`;
    document.getElementById('edit_name').value        = name;
    document.getElementById('edit_unit').value        = unit;
    document.getElementById('edit_price').value       = price;
    document.getElementById('edit_stock').value       = stock;
    document.getElementById('edit_description').value = desc;
    new bootstrap.Modal(document.getElementById('editMedicineModal')).show();
}

// ── DELETE ─────────────────────────────────────────────────────
function deleteMedicine(id, btn) {
    if (!confirm('Delete this medicine?')) return;
    fetch(`/hospital_admin/medicines/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) btn.closest('tr').remove();
    });
}
</script>
@endsection