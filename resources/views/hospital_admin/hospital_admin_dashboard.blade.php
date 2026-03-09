@extends('layouts.app1')

@section('title', 'Doctors list')

@section('content')

    <div class="container py-4">
        @php
            $bookingUrl = url('/hospital_booking/' . auth()->user()->hospital->hospital_code);
        @endphp

        <div class="d-flex align-items-center gap-2">
            <input type="text" id="bookingLink" class="form-control" value="{{ $bookingUrl }}" readonly>

            <button class="btn btn-primary" onclick="copyBookingLink()">
                Copy
            </button>
        </div>

        <small id="copyMsg" class="text-success d-none mt-1">
            Link copied!
        </small>
        <br><br>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h4 class="mb-0">Doctors</h4>
            <a class="btn add-btn mt-2 mt-md-0" href="{{ route('hospital_admin.doctors_form') }}">
                + Add Doctor
            </a>
            {{-- <p>{{auth()->user()->hospital->hospital_code}}</p> --}}
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0 p-md-3">

                <div class="table-responsive">
                    <table id="doctors_table" class="table table-hover table-bordered align-middle datatable mb-0">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Doctor Name</th>
                                <th>Doctor Phone</th>
                                <th>Experience</th>
                                <th>Specialization</th>
                                <th class="text-center no-sort">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- Sample data for testing --}}
                            @foreach ($doctors as $doctor)
                                <tr>
                                    <td>{{ $loop->index }}</td>
                                    <td>{{ $doctor->name }}</td>
                                    <td>{{ $doctor->phone }}</td>
                                    <td>{{ $doctor->doctor_code }}</td>
                                    <td>{{ $doctor->specialization }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2 flex-nowrap">
                                            <a href="{{ route('hospital_admin.doctors_edit_view', $doctor->id) }}"
                                                class="btn btn-sm btn-primary">
                                                Edit
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-danger"
                                                onclick="deletefn(this)" data-id="{{ $doctor->id }}"
                                                data-action="{{ route('hospital_admin.doctor_delete') }}">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            {{-- Add your foreach loop here --}}
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@push('scripts')
<script>
function copyBookingLink() {
    const input = document.getElementById('bookingLink');
    input.select();
    input.setSelectionRange(0, 99999); // mobile support

    navigator.clipboard.writeText(input.value).then(() => {
        const msg = document.getElementById('copyMsg');
        msg.classList.remove('d-none');

        setTimeout(() => {
            msg.classList.add('d-none');
        }, 2000);
    });
}
</script>

@endpush
@endsection
