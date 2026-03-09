@extends('layouts.app1')

@section('content')
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="w-100" style="max-width: 900px;">

            <h3 class="mb-4">Hospital Update Form</h3>

            <div class="card shadow-sm">
                <div class="card-body">
                    {{-- <h2>{{$data['id']}}</h2> --}}
                    <form action="{{ route('super_admin.hospital_edit', $data['id']) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

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
                                <label class="form-label fw-semibold">Hospital name</label>
                                <input type="text" name="hospital_name"
                                    class="form-control @error('hospital_name') is-invalid @enderror"
                                    value="{{ old('hospital_name', $data['hospital_name'] ?? '') }}">
                                @error('hospital_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hospital Phone --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Hospital phone</label>
                                <input type="text" name="hospital_phone"
                                    class="form-control @error('hospital_phone') is-invalid @enderror"
                                    value="{{ old('hospital_phone', $data['hospital_phone'] ?? '') }}">
                                @error('hospital_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Admin Name --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Admin name</label>
                                <input type="text" name="admin_name"
                                    class="form-control @error('admin_name') is-invalid @enderror"
                                    value="{{ old('admin_name', $data['admin_name'] ?? '') }}">
                                @error('admin_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Admin Phone --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Admin phone</label>
                                <input type="text" name="admin_phone"
                                    class="form-control @error('admin_phone') is-invalid @enderror"
                                    value="{{ old('admin_phone', $data['admin_phone'] ?? '') }}">
                                @error('admin_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                             <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Flow id</label>
                                <input type="text" name="flow_id"
                                    class="form-control @error('flow_id') is-invalid @enderror"
                                    value="{{ old('flow_id', $data['flow_id'] ?? '') }}">
                                @error('flow_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- City --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" name="city"
                                    class="form-control @error('city') is-invalid @enderror"
                                    value="{{ old('city', $data['city'] ?? '') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Country --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Country</label>
                                <input type="text" name="country"
                                    class="form-control @error('country') is-invalid @enderror"
                                    value="{{ old('country', $data['country'] ?? '') }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Country</label>
                                <input type="text" name="country"
                                    class="form-control @error('country') is-invalid @enderror"
                                    value="{{ old('country', $data['country'] ?? '') }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Address Line 1 --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Address line 1</label>
                                <textarea name="address_line" class="form-control @error('address_line') is-invalid @enderror" rows="2">{{ old('address_line', $data['address_line'] ?? '') }}</textarea>
                                @error('address_line')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Address Line 2 --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Address line 2</label>
                                <textarea name="address_line2" class="form-control @error('address_line2') is-invalid @enderror" rows="2">{{ old('address_line2', $data['address_line2'] ?? '') }}</textarea>
                                @error('address_line2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- DB Status --}}
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
                                @enderror
                            </div>

                            {{-- Active --}}
                            <div class="col-md-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', $data['is_active'] ?? '') == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold">Active</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-4">
                                {{-- @if (!empty($data['hospital_logo'])) --}}
                                    @if (!empty($data['hospital_logo']))
                                        <img src="{{ Storage::disk('s3')->url($data['hospital_logo']) }}"
                                            alt="Hospital Logo" class="img_load hospital-logo img-fluid" loading="lazy">
                                    @endif
                                {{-- @endif --}}
                            </div>

                            {{-- Hospital Logo --}}
                            <div class="col-md-12 mb-4">
                                <label class="form-label fw-semibold">Hospital Logo</label>
                                <input type="file" name="hospital_logo"
                                    class="form-control img_change @error('hospital_logo') is-invalid @enderror">
                                @error('hospital_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4" style="background-color: #1363C6;">
                                Update Hospital
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
@endsection
