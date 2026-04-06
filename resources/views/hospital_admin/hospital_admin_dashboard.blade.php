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

        {{-- ── API Token Card ── --}}
        <div class="mt-3 mb-4 p-3 rounded-3" style="background:#f0f6ff;border:1px solid #b5d4f4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span style="font-size:13px;font-weight:600;color:#0C447C">API Token</span>
                        <span style="font-size:11px;background:#E6F1FB;color:#185FA5;padding:1px 8px;border-radius:999px;font-weight:500">Use in Swagger / cURL</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="password" id="apiTokenInput" class="form-control form-control-sm" readonly
                               value="{{ auth()->user()->api_code }}"
                               style="font-family:monospace;font-size:13px;width:320px;background:#fff;border-color:#b5d4f4">
                        <button class="btn btn-sm" onclick="toggleApiToken()"
                                style="background:#E6F1FB;color:#0C447C;border:1px solid #b5d4f4;font-size:12px"
                                id="toggleBtn">Show</button>
                        <button class="btn btn-sm" onclick="copyApiToken()"
                                style="background:#1363C6;color:#fff;font-size:12px"
                                id="copyTokenBtn">Copy</button>
                    </div>
                    <small class="text-muted mt-1 d-block" style="font-size:11px">
                        Use this as <code>Authorization: Bearer {token}</code> in API requests &mdash;
                        <a href="/api/documentation" target="_blank" style="color:#1363C6">Open Swagger UI</a>
                    </small>
                </div>
            </div>
        </div>

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
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        const msg = document.getElementById('copyMsg');
        msg.classList.remove('d-none');
        setTimeout(() => msg.classList.add('d-none'), 2000);
    });
}

function toggleApiToken() {
    const input = document.getElementById('apiTokenInput');
    const btn   = document.getElementById('toggleBtn');
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = 'Hide';
    } else {
        input.type = 'password';
        btn.textContent = 'Show';
    }
}

function copyApiToken() {
    const input = document.getElementById('apiTokenInput');
    const type  = input.type;
    input.type  = 'text';
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        input.type = type;
        const btn  = document.getElementById('copyTokenBtn');
        btn.textContent = 'Copied!';
        btn.style.background = '#198754';
        setTimeout(() => {
            btn.textContent = 'Copy';
            btn.style.background = '#1363C6';
        }, 2000);
    });
}
</script>

@endpush
@endsection