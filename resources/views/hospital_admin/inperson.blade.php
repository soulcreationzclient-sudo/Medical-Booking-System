@extends('layouts.app1')
@section('title','Overall Patient Bookings')
@section('content')

<div class="container py-4">
    @include('components.toast')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-black"><i class="fas fa-calendar-plus me-2"></i>Book an Appointment</h5>
        </div>
        <div class="card-body">

            {{-- Success Alert --}}
            <div id="successAlert" class="alert alert-success d-none">
                <strong>Booking Confirmed!</strong> Your booking code is:
                <span id="bookingCode" class="fw-bold text-primary"></span>
            </div>

            {{-- Error Alert --}}
            <div id="errorAlert" class="alert alert-danger d-none"></div>

            <form id="bookingForm" action="{{ route('hospital_admin.inperson') }}" method="POST">
                @csrf

                <h6 class="text-muted mb-3 border-bottom pb-2">Patient Information</h6>
                <div class="row g-3">

                    {{-- Patient Name --}}
                    <div class="col-md-6">
                        <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                        <input type="text" name="patient_name" id="patient_name"
                               class="form-control @error('patient_name') is-invalid @enderror"
                               placeholder="Full name" required value="{{ old('patient_name') }}">
                        @error('patient_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     {{-- Patient Email --}}
                    <div class="col-md-6">
                        <label class="form-label">Patient Email <span class="text-danger">*</span></label>
                        <input type="text" name="patient_email" id="patient_email"
                               class="form-control @error('patient_email') is-invalid @enderror"
                               placeholder="Full name" required value="{{ old('patient_email') }}">
                        @error('patient_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div class="col-md-6">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" name="patient_phone" id="patient_phone"
                               class="form-control @error('patient_phone') is-invalid @enderror"
                               placeholder="919994780436" required value="{{ old('patient_phone') }}">
                        @error('patient_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Assigned Doctor --}}
                    <div class="col-md-6">
                        <label class="form-label">Assigned to Doctor <span class="text-danger">*</span></label>
                        <select name="doctor_id" id="doctor_id"
                                class="form-control @error('doctor_id') is-invalid @enderror">
                            <option value="">-- Select Doctor --</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}"
                                    {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Age --}}
                    <div class="col-md-6">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" id="age"
                               class="form-control @error('age') is-invalid @enderror"
                               placeholder="Age" min="0" max="150" value="{{ old('age') }}">
                        @error('age')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Cause --}}
                    <div class="col-12">
                        <label class="form-label">Cause / Reason for Visit</label>
                        <textarea name="cause" id="cause" rows="2"
                                  class="form-control @error('cause') is-invalid @enderror"
                                  placeholder="Describe your symptoms or reason...">{{ old('cause') }}</textarea>
                        @error('cause')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <h6 class="text-muted mt-4 mb-3 border-bottom pb-2">Appointment Details</h6>
                <div class="row g-3">

                    {{-- Booking Date --}}
                    <div class="col-md-6">
                        <label class="form-label">Booking Date <span class="text-danger">*</span></label>
                        <input type="date" name="booking_date" id="booking_date"
                               class="form-control @error('booking_date') is-invalid @enderror"
                               required min="{{ date('Y-m-d') }}" value="{{ old('booking_date') }}">
                        @error('booking_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Start Time --}}
                    <div class="col-md-6">
                        <label class="form-label">Start Time <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" id="start_time"
                               class="form-control @error('start_time') is-invalid @enderror"
                               required value="{{ old('start_time') }}">
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                        <span id="submitText">
                            <i class="fas fa-check-circle me-1"></i>Confirm Booking
                        </span>
                        <span id="submitSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span> Processing...
                        </span>
                    </button>
                    <button type="reset" class="btn btn-outline-secondary px-4">Clear</button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
