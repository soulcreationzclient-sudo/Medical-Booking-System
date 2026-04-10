@extends('layouts.app1')

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background:#1363C6;">
            <h5 class="mb-0">Treatments</h5>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addTreatmentModal">
                + Add Treatment
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Category</th>
                            <th>Base Price</th>
                            <th>Status</th>
                            <th width="180"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($treatments as $treatment)
                            <tr>
                                <td>{{ $treatment->name }}</td>
                                <td>{{ $treatment->code ?: '—' }}</td>
                                <td class="text-capitalize">{{ $treatment->category }}</td>
                                <td>RM{{ number_format($treatment->base_price, 2) }}</td>
                                <td>
                                    @if($treatment->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editTreatmentModal{{ $treatment->id }}">
                                        Edit
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="deleteTreatment({{ $treatment->id }})">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No treatments added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Edit Modals moved OUTSIDE table --}}
@foreach($treatments as $treatment)
    <div class="modal fade" id="editTreatmentModal{{ $treatment->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('hospital_admin.treatments.update', $treatment->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Treatment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $treatment->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" class="form-control" value="{{ $treatment->code }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                @foreach(['consultation','treatment','operation','medicine','other'] as $category)
                                    <option value="{{ $category }}" @selected($treatment->category === $category)>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Base Price</label>
                            <input type="number" name="base_price" class="form-control" step="0.01" min="0" value="{{ $treatment->base_price }}" required>
                        </div>

                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox"
                                   class="form-check-input"
                                   name="is_active"
                                   value="1"
                                   id="isActive{{ $treatment->id }}"
                                   @checked($treatment->is_active)>
                            <label for="isActive{{ $treatment->id }}" class="form-check-label">Active</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<div class="modal fade" id="addTreatmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('hospital_admin.treatments.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Treatment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" name="code" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select" required>
                            <option value="consultation">Consultation</option>
                            <option value="treatment" selected>Treatment</option>
                            <option value="operation">Operation</option>
                            <option value="medicine">Medicine</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Base Price</label>
                        <input type="number" name="base_price" class="form-control" step="0.01" min="0" required>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" id="newTreatmentActive" checked>
                        <label for="newTreatmentActive" class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Treatment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteTreatment(id) {
    if (!confirm('Delete this treatment?')) return;

    fetch(`/hospital_admin/treatments/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).then(() => window.location.reload());
}
</script>
@endsection