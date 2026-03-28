@extends('layouts.app1')
@section('title','Book an Appointment')
@section('content')

<div class="container py-4">
    @include('components.toast')

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Book an Appointment</h5>
        </div>
        <div class="card-body">

            {{-- Alerts --}}
            <div id="successAlert" class="alert alert-success d-none">
                <strong>Booking Confirmed!</strong> Code: <span id="bookingCode" class="fw-bold text-primary"></span>
            </div>
            <div id="errorAlert" class="alert alert-danger d-none"></div>

            {{-- Pre-fill banner --}}
            <div id="prefillBanner" class="alert alert-info d-none">
                <i class="fas fa-info-circle me-2"></i>
                Patient found — form pre-filled with existing data.
            </div>

            <form id="bookingForm" action="{{ route('hospital_admin.inperson') }}" method="POST">
                @csrf

                {{-- ══ PATIENT INFORMATION ══ --}}
                <h6 class="text-muted mb-3 border-bottom pb-2">
                    <i class="fas fa-user me-1"></i> Patient Information
                </h6>
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                        <input type="text" name="patient_name" id="patient_name"
                               class="form-control @error('patient_name') is-invalid @enderror"
                               placeholder="Full name" required value="{{ old('patient_name') }}">
                        @error('patient_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">IC / Passport No</label>
                        <input type="text" name="ic_passport_no" id="ic_passport_no"
                               class="form-control @error('ic_passport_no') is-invalid @enderror"
                               placeholder="e.g. 901231-14-5678" value="{{ old('ic_passport_no') }}">
                        @error('ic_passport_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" name="patient_phone" id="patient_phone"
                               class="form-control @error('patient_phone') is-invalid @enderror"
                               placeholder="e.g. 919994780436" required value="{{ old('patient_phone') }}">
                        @error('patient_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Patient Email</label>
                        <input type="email" name="patient_email" id="patient_email"
                               class="form-control @error('patient_email') is-invalid @enderror"
                               placeholder="email@example.com" value="{{ old('patient_email') }}">
                        @error('patient_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" id="age"
                               class="form-control @error('age') is-invalid @enderror"
                               placeholder="Age" min="0" max="150" value="{{ old('age') }}">
                        @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" id="dob"
                               class="form-control @error('dob') is-invalid @enderror"
                               value="{{ old('dob') }}">
                        @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" id="gender"
                                class="form-control @error('gender') is-invalid @enderror">
                            <option value="">-- Select --</option>
                            <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other"  {{ old('gender') == 'other'  ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Blood Type</label>
                        <select name="blood_type" id="blood_type"
                                class="form-control @error('blood_type') is-invalid @enderror">
                            <option value="">-- Select --</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                                <option value="{{ $bt }}" {{ old('blood_type') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                        @error('blood_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Marital Status</label>
                        <select name="marital_status" id="marital_status"
                                class="form-control @error('marital_status') is-invalid @enderror">
                            <option value="">-- Select --</option>
                            <option value="single"   {{ old('marital_status') == 'single'   ? 'selected' : '' }}>Single</option>
                            <option value="married"  {{ old('marital_status') == 'married'  ? 'selected' : '' }}>Married</option>
                            <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="widowed"  {{ old('marital_status') == 'widowed'  ? 'selected' : '' }}>Widowed</option>
                        </select>
                        @error('marital_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nationality</label>
                        <input type="text" name="nationality" id="nationality"
                               class="form-control @error('nationality') is-invalid @enderror"
                               placeholder="e.g. Malaysian" value="{{ old('nationality', 'Malaysian') }}">
                        @error('nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Assigned to Doctor <span class="text-danger">*</span></label>
                        <select name="doctor_id" id="doctor_id"
                                class="form-control @error('doctor_id') is-invalid @enderror" required>
                            <option value="">-- Select Doctor --</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Cause / Reason for Visit</label>
                        <textarea name="cause" id="cause" rows="2"
                                  class="form-control @error('cause') is-invalid @enderror"
                                  placeholder="Describe symptoms or reason...">{{ old('cause') }}</textarea>
                        @error('cause')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                </div>

                {{-- ══ EMERGENCY CONTACT ══ --}}
                <h6 class="text-muted mt-4 mb-3 border-bottom pb-2">
                    <i class="fas fa-phone-alt me-1"></i> Emergency Contact
                </h6>
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name"
                               class="form-control" placeholder="Full name"
                               value="{{ old('emergency_contact_name') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Emergency Contact No</label>
                        <input type="tel" name="emergency_contact_no" id="emergency_contact_no"
                               class="form-control" placeholder="Phone number"
                               value="{{ old('emergency_contact_no') }}">
                    </div>

                </div>

                {{-- ══ ADDRESS ══ --}}
                <h6 class="text-muted mt-4 mb-3 border-bottom pb-2">
                    <i class="fas fa-map-marker-alt me-1"></i> Address
                </h6>
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" id="address" rows="2"
                                  class="form-control @error('address') is-invalid @enderror"
                                  placeholder="Street address...">{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city" id="city"
                               class="form-control" placeholder="City"
                               value="{{ old('city') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state" id="state"
                               class="form-control" placeholder="State"
                               value="{{ old('state') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Postcode</label>
                        <input type="text" name="postcode" id="postcode"
                               class="form-control" placeholder="Postcode"
                               value="{{ old('postcode') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" id="country"
                               class="form-control" placeholder="Country"
                               value="{{ old('country', 'Malaysia') }}">
                    </div>

                </div>

                {{-- ══ APPOINTMENT DETAILS ══ --}}
                <h6 class="text-muted mt-4 mb-3 border-bottom pb-2">
                    <i class="fas fa-clock me-1"></i> Appointment Details
                </h6>
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Booking Date <span class="text-danger">*</span></label>
                        <input type="date" name="booking_date" id="booking_date"
                               class="form-control @error('booking_date') is-invalid @enderror"
                               required min="{{ date('Y-m-d') }}" value="{{ old('booking_date') }}">
                        @error('booking_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Start Time <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" id="start_time"
                               class="form-control @error('start_time') is-invalid @enderror"
                               required value="{{ old('start_time') }}">
                        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                        <i class="fas fa-check-circle me-1"></i>Confirm Booking
                    </button>
                    <button type="reset" class="btn btn-outline-secondary px-4">Clear</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
// ── DOB → Age auto-calculation ──
document.getElementById('dob').addEventListener('change', function () {
    const dob = new Date(this.value);
    if (!this.value || isNaN(dob)) return;
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    document.getElementById('age').value = age;
});

// ── PRE-FILL: look up patient when phone field loses focus ──
document.getElementById('patient_phone').addEventListener('blur', function () {
    const phone = this.value.trim();
    if (!phone) return;

    fetch(`/booking/lookup-patient?phone=${encodeURIComponent(phone)}&hospital_id={{ auth()->user()->hospital_id }}`, {
        headers: { 'Accept': 'application/json',
                   'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.found) return;
        const p = data.patient;

        const fill = (id, val) => { const el = document.getElementById(id); if (el && val) el.value = val; };
        const fillSel = (id, val) => {
            const el = document.getElementById(id);
            if (!el || !val) return;
            [...el.options].forEach(o => { o.selected = o.value === val; });
        };

        fill('patient_name', p.name);
        fill('ic_passport_no', p.ic_passport_no);
        fill('age', p.age);
        fill('dob', p.dob);
        fillSel('gender', p.gender);
        fillSel('blood_type', p.blood_type);
        fillSel('marital_status', p.marital_status);
        fill('nationality', p.nationality);
        fill('emergency_contact_name', p.emergency_contact_name);
        fill('emergency_contact_no', p.emergency_contact_no);
        fill('address', p.address);
        fill('city', p.city);
        fill('state', p.state);
        fill('postcode', p.postcode);
        fill('country', p.country);

        document.getElementById('prefillBanner').classList.remove('d-none');
    })
    .catch(() => {});
});
</script>

@endsection