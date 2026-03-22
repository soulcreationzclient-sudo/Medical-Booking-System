@extends('layouts.app2')

@section('content')
<div class="container-fluid py-4">

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card border-0 shadow-sm" style="max-width:860px;margin:auto;">
        <div class="card-header bg-info text-white py-3">
            <h5 class="mb-0 fw-bold">📋 Add Prescription</h5>
            <small class="opacity-75">
                Booking #{{ $booking->id }} &mdash;
                {{ $booking->patient_name }} &mdash;
                {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}
            </small>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('hospital_admin.prescriptions.store', $booking->id) }}">
                @csrf

                {{-- MEDICINES TABLE --}}
                <h6 class="fw-bold mb-3 text-primary">Medicines</h6>
                <div id="medicinesContainer">
                    <div class="medicine-row row g-2 align-items-end mb-2 border rounded-3 p-2" id="medRow0">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Medicine <span class="text-danger">*</span></label>
                            <select name="medicines[0][medicine_id]" class="form-select medicine-select" required
                                    onchange="autofillPrice(this, 0)">
                                <option value="">— Select —</option>
                                @foreach($medicines as $m)
                                <option value="{{ $m->id }}"
                                        data-price="{{ $m->price }}"
                                        data-stock="{{ $m->stock }}"
                                        data-unit="{{ $m->unit }}">
                                    {{ $m->name }} ({{ $m->unit }}) — ₹{{ $m->price }} | Stock: {{ $m->stock }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Qty <span class="text-danger">*</span></label>
                            <input type="number" name="medicines[0][quantity]" class="form-control qty-input"
                                   min="1" value="1" required onchange="updateLineTotal(0)">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Dosage Instructions</label>
                            <input type="text" name="medicines[0][dosage]" class="form-control"
                                   placeholder="e.g. 1 tab morning">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Line Total</label>
                            <div class="form-control bg-light text-center fw-bold" id="lineTotal0">₹0.00</div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeRow(this)">✕</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm mt-2 mb-4" onclick="addMedicineRow()">
                    + Add Another Medicine
                </button>

                {{-- GRAND TOTAL --}}
                <div class="alert alert-primary d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Total Medicine Cost:</span>
                    <span class="fw-bold fs-5" id="grandTotal">₹0.00</span>
                </div>

                {{-- NOTES --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Doctor's Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="3"
                              placeholder="Additional instructions, diagnosis notes..."></textarea>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-info text-white fw-semibold">💾 Save Prescription</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// All medicines data for JS price lookup
const medicinesData = @json($medicines->keyBy('id'));
let rowCount = 1;

function autofillPrice(select, rowIndex) {
    updateLineTotal(rowIndex);
}

function updateLineTotal(rowIndex) {
    const row      = document.getElementById(`medRow${rowIndex}`);
    if (!row) return;
    const select   = row.querySelector('.medicine-select');
    const qtyInput = row.querySelector('.qty-input');
    const lineTotalEl = document.getElementById(`lineTotal${rowIndex}`);

    const selectedOption = select.options[select.selectedIndex];
    const price  = parseFloat(selectedOption?.dataset?.price || 0);
    const qty    = parseInt(qtyInput?.value || 1);
    const stock  = parseInt(selectedOption?.dataset?.stock || 0);
    const total  = price * qty;

    if (lineTotalEl) lineTotalEl.textContent = `₹${total.toFixed(2)}`;

    // Warn if exceeds stock
    if (qty > stock && stock > 0) {
        qtyInput.classList.add('is-invalid');
        qtyInput.title = `Only ${stock} in stock`;
    } else {
        qtyInput.classList.remove('is-invalid');
    }

    recalcGrandTotal();
}

function recalcGrandTotal() {
    let grand = 0;
    document.querySelectorAll('[id^="lineTotal"]').forEach(el => {
        grand += parseFloat(el.textContent.replace('₹','') || 0);
    });
    document.getElementById('grandTotal').textContent = `₹${grand.toFixed(2)}`;
}

function addMedicineRow() {
    const idx = rowCount++;
    const template = document.getElementById('medRow0').cloneNode(true);
    template.id = `medRow${idx}`;

    // Update input names
    template.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace('[0]', `[${idx}]`);
        if (el.tagName === 'INPUT') el.value = el.type === 'number' ? 1 : '';
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
        if (el.classList.contains('medicine-select')) {
            el.setAttribute('onchange', `autofillPrice(this, ${idx})`);
        }
        if (el.classList.contains('qty-input')) {
            el.setAttribute('onchange', `updateLineTotal(${idx})`);
        }
    });

    // Reset line total display
    const lineTotalEl = template.querySelector('[id^="lineTotal"]');
    if (lineTotalEl) {
        lineTotalEl.id = `lineTotal${idx}`;
        lineTotalEl.textContent = '₹0.00';
    }

    document.getElementById('medicinesContainer').appendChild(template);
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.medicine-row');
    if (rows.length <= 1) { alert('At least one medicine is required.'); return; }
    btn.closest('.medicine-row').remove();
    recalcGrandTotal();
}
</script>
@endsection