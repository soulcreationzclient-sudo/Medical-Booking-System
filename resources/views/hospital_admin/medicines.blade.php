@extends('layouts.app1')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">

        {{-- LEFT: TABLE --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white d-flex justify-content-between align-items-center py-3" style="background:#1363C6;">
                    <h5 class="mb-0 fw-bold">Medicines</h5>
                    <button class="btn btn-light btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                        + Add Medicine
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr style="background:#1363C6;color:#fff;">
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Unit</th>
                                    <th>Price (RM)</th>
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
                                    <td>RM{{ number_format($med->price, 2) }}</td>
                                    <td>
                                        @if($med->stock <= 5)
                                            <span class="badge bg-danger">{{ $med->stock }}</span>
                                        @elseif($med->stock <= 20)
                                            <span class="badge bg-warning text-dark">{{ $med->stock }}</span>
                                        @else
                                            <span class="badge bg-success">{{ $med->stock }}</span>
                                        @endif
                                    </td>
                                    <td style="white-space:nowrap">
                                        <button class="btn btn-sm btn-primary me-1"
                                            onclick="toggleEditRow({{ $med->id }})">Edit</button>
                                        <button class="btn btn-sm btn-warning me-1"
                                            onclick="toggleStockRow({{ $med->id }})">Stock</button>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="deleteMedicine({{ $med->id }}, this)">Delete</button>
                                    </td>
                                </tr>

                                {{-- INLINE EDIT ROW --}}
                                <tr id="edit-row-{{ $med->id }}" style="display:none;background:#f0f7ff;">
                                    <td colspan="6" class="p-3">
                                        <form method="POST" action="{{ route('hospital_admin.medicines.update', $med->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="stock" value="{{ $med->stock }}">
                                            <div class="row g-2 align-items-end">
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-semibold mb-1">Name *</label>
                                                    <input type="text" name="name" class="form-control form-control-sm"
                                                           value="{{ $med->name }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-semibold mb-1">Unit *</label>
                                                    <select name="unit" class="form-select form-select-sm" required>
                                                        @foreach(['tablet','capsule','syrup (ml)','injection (ml)','sachet','tube','strip'] as $u)
                                                            <option value="{{ $u }}" {{ $med->unit === $u ? 'selected' : '' }}>{{ $u }}</option>
                                                        @endforeach
                                                        @if(!in_array($med->unit, ['tablet','capsule','syrup (ml)','injection (ml)','sachet','tube','strip']))
                                                            <option value="{{ $med->unit }}" selected>{{ $med->unit }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-semibold mb-1">Price (RM) *</label>
                                                    <input type="number" name="price" class="form-control form-control-sm"
                                                           value="{{ $med->price }}" step="0.01" min="0" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-semibold mb-1">Description</label>
                                                    <input type="text" name="description" class="form-control form-control-sm"
                                                           value="{{ $med->description ?? '' }}" placeholder="Optional">
                                                </div>
                                                <div class="col-md-2 d-flex gap-1">
                                                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        onclick="toggleEditRow({{ $med->id }})">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>

                                {{-- INLINE STOCK ROW --}}
                                <tr id="stock-row-{{ $med->id }}" style="display:none;background:#fffbeb;">
                                    <td colspan="6" class="p-3">
                                        <form method="POST" action="{{ route('hospital_admin.medicines.update', $med->id) }}"
                                              id="stock-form-{{ $med->id }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="name"        value="{{ $med->name }}">
                                            <input type="hidden" name="unit"        value="{{ $med->unit }}">
                                            <input type="hidden" name="price"       value="{{ $med->price }}">
                                            <input type="hidden" name="description" value="{{ $med->description ?? '' }}">
                                            <input type="hidden" name="stock"       id="final-stock-{{ $med->id }}" value="{{ $med->stock }}">

                                            <div class="row g-2 align-items-end">
                                                <div class="col-auto">
                                                    <label class="form-label small fw-semibold mb-1">Current Stock</label>
                                                    <input type="number" class="form-control form-control-sm bg-light"
                                                           value="{{ $med->stock }}" disabled style="width:90px;">
                                                </div>
                                                <div class="col-auto">
                                                    <label class="form-label small fw-semibold mb-1">Action</label>
                                                    <select class="form-select form-select-sm" id="action-{{ $med->id }}"
                                                            onchange="calcStock({{ $med->id }}, {{ $med->stock }})" style="width:140px;">
                                                        <option value="add">Add Stock</option>
                                                        <option value="remove">Remove Stock</option>
                                                        <option value="set">Set Exact</option>
                                                    </select>
                                                </div>
                                                <div class="col-auto">
                                                    <label class="form-label small fw-semibold mb-1">Quantity</label>
                                                    <input type="number" class="form-control form-control-sm"
                                                           id="qty-{{ $med->id }}" value="0" min="0" style="width:90px;"
                                                           oninput="calcStock({{ $med->id }}, {{ $med->stock }})">
                                                </div>
                                                <div class="col-auto">
                                                    <label class="form-label small fw-semibold mb-1">New Stock</label>
                                                    <input type="number" class="form-control form-control-sm"
                                                           id="preview-{{ $med->id }}" value="{{ $med->stock }}"
                                                           style="width:90px;background:#f0fdf4;" readonly>
                                                </div>
                                                <div class="col-auto d-flex gap-1 align-self-end">
                                                    <button type="submit" class="btn btn-warning btn-sm">Update</button>
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        onclick="toggleStockRow({{ $med->id }})">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
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

        {{-- RIGHT: PIE CHART --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white py-3" style="background:#1363C6;">
                    <h5 class="mb-0 fw-bold">Stock Distribution</h5>
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

            @php $lowStock = $medicines->where('stock', '<=', 5); @endphp
            @if($lowStock->count())
            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-danger text-white py-2 fw-semibold">Low Stock Alerts</div>
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

{{-- ADD MEDICINE MODAL --}}
<div class="modal fade" id="addMedicineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('hospital_admin.medicines.store') }}">
                @csrf
                <div class="modal-header text-white" style="background:#1363C6;">
                    <h5 class="modal-title fw-bold">Add Medicine</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Paracetamol" required>
                    </div>
                    <div class="row g-2 mb-3">
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
                            <label class="form-label fw-semibold">Price per unit (RM) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Initial Stock <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control" min="0" placeholder="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description (optional)</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-semibold">Add Medicine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

@if($medicines->count())
const stockData = @json($stockData);
new Chart(document.getElementById('stockPieChart'), {
    type: 'pie',
    data: {
        labels: stockData.map(m => m.name),
        datasets: [{
            data: stockData.map(m => m.stock),
            backgroundColor: [
                '#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6',
                '#ec4899','#06b6d4','#84cc16','#f97316','#a78bfa'
            ],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} units` } }
        }
    }
});
@endif

function toggleEditRow(id) {
    const row      = document.getElementById('edit-row-'  + id);
    const stockRow = document.getElementById('stock-row-' + id);
    if (stockRow) stockRow.style.display = 'none';
    row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
}

function toggleStockRow(id) {
    const row     = document.getElementById('stock-row-' + id);
    const editRow = document.getElementById('edit-row-'  + id);
    if (editRow) editRow.style.display = 'none';
    row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
}

function calcStock(id, currentStock) {
    const action  = document.getElementById('action-'       + id).value;
    const qty     = parseInt(document.getElementById('qty-' + id).value) || 0;
    const preview = document.getElementById('preview-'      + id);
    const final   = document.getElementById('final-stock-'  + id);

    let newStock = currentStock;
    if (action === 'add')    newStock = currentStock + qty;
    if (action === 'remove') newStock = Math.max(0, currentStock - qty);
    if (action === 'set')    newStock = qty;

    preview.value = newStock;
    final.value   = newStock;

    if (newStock <= 5)       preview.style.background = '#fee2e2';
    else if (newStock <= 20) preview.style.background = '#fef9c3';
    else                     preview.style.background = '#f0fdf4';
}

function deleteMedicine(id, btn) {
    if (!confirm('Delete this medicine?')) return;
    fetch(`/hospital_admin/medicines/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => { if (data.success) btn.closest('tr').remove(); });
}
</script>
@endsection