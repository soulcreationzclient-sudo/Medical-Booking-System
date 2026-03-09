@extends('layouts.app1')

@section('title', 'Hospitals and hospital admin list')

@section('content')

    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h4 class="mb-0">Specialization list</h4>
            <button class="btn add-btn mt-2 mt-md-0" data-bs-toggle="modal" data-bs-target="#addSpecialization">
                + Add Specialization
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0 p-md-3">

                <div class="table-responsive">
                    <table id="doctors_table" class="table table-hover table-bordered align-middle datatable mb-0">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Specialization</th>
                                <th>Description</th>
                                <th class="text-center no-sort">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($specialization as $s)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $s->specialization }}</td>
                                    <td>{{ $s->description }}</td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2 flex-nowrap">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#editSpecialization" data-id="{{ $s->id }}"
                                                data-name="{{ $s->specialization }}" data-description="{{ $s->description }}">
                                                Edit
                                            </button>
                                            <a href__="javascript:void(0)" class="btn btn-sm btn-danger"
                                                onclick="deletefn(this)" data-id="{{ $s->id }}"
                                                data-action="{{ route('hospital_admin.specialization_delete') }}"
                                                data-method="POST">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <x-pop_up id="addSpecialization" title="Add Specialization">

        {{-- BODY CONTENT --}}
        <form action="{{ route('hospital_admin.specialization_add') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Specialization</label>
                <input type="text" name="specialization" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

        </form>

        {{-- FOOTER SLOT --}}
        <x-slot name="footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-primary" onclick="document.querySelector('#addSpecialization form').submit()">
                Save
            </button>
        </x-slot>

    </x-pop_up>

    {{-- EDIT SPECIALIZATION --}}
    <x-pop_up id="editSpecialization" title="Edit Specialization">

        <form action="{{ route('hospital_admin.specialization_edit') }}" method="POST" id="editSpecializationForm">
            @csrf

            <input type="hidden" name="id" id="specialization_id">

            <div class="mb-3">
                <label class="form-label">Specialization</label>
                <input type="text" name="specialization" id="specialization_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" id="specialization_description" class="form-control" rows="3"></textarea>
            </div>
        </form>

        <x-slot name="footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-primary" onclick="document.getElementById('editSpecializationForm').submit()">
                Save
            </button>
        </x-slot>

    </x-pop_up>

    {{-- DataTable Init --}}
    @push('scripts')
        <script>
            const editModal = document.getElementById('editSpecialization');

            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                document.getElementById('specialization_id').value =
                    button.getAttribute('data-id');

                document.getElementById('specialization_name').value =
                    button.getAttribute('data-name') ?? '';

                document.getElementById('specialization_description').value =
                    button.getAttribute('data-description') ?? '';
            });
        </script>
    @endpush

@endsection
