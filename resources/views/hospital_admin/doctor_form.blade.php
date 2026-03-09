@extends('layouts.app1')

@section('content')
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="w-100" style="max-width: 900px;">

            <h3 class="mb-4">{{ $title }}</h3>

            <div class="card shadow-sm">
                <div class="card-body ">

                    <form action="{{ route($route, $data['id']??'') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        @csrf
                        <div class="row">

                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="text" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $data['email'] ?? '') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Leave blank to keep existing password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hospital Name --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Doctor name</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $data['name'] ?? '') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hospital Phone --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Doctor phone</label>
                                <input type="text" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $data['phone'] ?? '') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Gender --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Gender</label>
                                <br>
                                <input type="radio" name="gender" value="male" @checked(old('gender', $data['gender'] ?? '') =='male')>
                                Male

                                <input type="radio" name="gender" value="female" class="ms-4"
                                    @checked(old('gender', $data['gender'] ?? '') === 'female')>
                                Female

                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- EXP --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Experience</label>
                                <input type="number" name="experience_years"
                                    class="form-control @error('experience_years') is-invalid @enderror"
                                    value="{{ old('experience_years', $data['experience_years'] ?? '') }}">
                                @error('experience_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- City --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Qualification</label>
                                <input type="text" name="qualification"
                                    class="form-control @error('qualification') is-invalid @enderror"
                                    value="{{ old('qualification', $data['qualification'] ?? '') }}">
                                @error('qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- SPECIALIZATIOn --}}
                            {{-- SPECIALIZATION --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Specialization</label>

                                <select name="specialization"
                                    class="form-control select2 @error('specialization') is-invalid @enderror">
                                    <option value="">-- Select Specialization --</option>

                                    @foreach ($specialization as $list)
                                        <option value="{{ $list->id }}" @selected(old('specialization', $data['specialization_id'] ?? '') == $list->id)>
                                            {{ $list->specialization }}
                                        </option>
                                    @endforeach
                                </select>


                                @error('specialization')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Country
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Country</label>
                                <input type="text" name="country"
                                    class="form-control @error('country') is-invalid @enderror"
                                    value="{{ old('country', $data['country'] ?? '') }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}

                            {{-- Address Line 1
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Address line 1</label>
                                <textarea name="address_line" class="form-control @error('address_line') is-invalid @enderror" rows="2">{{ old('address_line', $data['address_line'] ?? '') }}</textarea>
                                @error('address_line')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}

                            {{-- Address Line 2
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Address line 2</label>
                                <textarea name="address_line2" class="form-control @error('address_line2') is-invalid @enderror" rows="2">{{ old('address_line2', $data['address_line2'] ?? '') }}</textarea>
                                @error('address_line2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}

                            {{-- DB Status
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold d-block">DB Status</label>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="db_status" value="0"
                                        {{ old('db_status', $data['db_status'] ?? '') == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label">Testing</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="db_status" value="1"
                                        {{ old('db_status', $data['db_status'] ?? '') == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label">Production</label>
                                </div>

                                @error('db_status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror --}}
                        </div>

                        {{-- Active --}}
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" value="1"
                                    {{ old('status', $data['status'] ?? '') == 1 ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold">Active</label>
                            </div>
                        </div>


                        {{-- Hospital Logo --}}
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-semibold">Doctor photo</label>
                            <input type="file" name="profile_photo"
                                class="form-control img_change @error('profile_photo') is-invalid @enderror">
                            @error('profile_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-4">
                            <img src="{{ !empty($data['profile_photo']) ? Storage::disk('s3')->url($data['profile_photo']) : '' }}"
                                alt="Hospital Logo" id="hospital_logo"
                                class="img_load hospital-logo  {{ empty($data['profile_photo']) ? 'd-none' : '' }}">
                        </div>


                </div>

                <div class="text-end mb-4 pe-3">
                    <button type="submit" class="btn btn-primary px-4">
                        {{ $button }}
                    </button>
                </div>

                </form>

            </div>
        </div>

    </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: '-- Select Specialization --',
                    allowClear: true,
                    width: '100%'
                });
            })
        </script>
    @endpush
@endsection
