@extends('layouts.app1')

@section('title', 'Hospitals')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    body {
        background: #f4f7fb;
        font-family: 'Open Sans', sans-serif;
    }

    /* ===== PAGE WRAPPER ===== */
    .page-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 32px 24px;
    }

    /* ===== PAGE HEADER (LIKE LOGIN LEFT PANEL CALMNESS) ===== */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-header h2 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
    }

    .add-hospital-btn {
        background: #1363C6;
        color: #ffffff;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        white-space: nowrap;
    }

    .add-hospital-btn:hover {
        background: #0f52a5;
        color: #ffffff;
    }

    /* ===== CARD ===== */
    .content-card {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        padding: 20px;
    }

    /* ===== DATATABLE FIXES ===== */
    .dataTables_wrapper {
        font-size: 14px;
        color: #1f2937;
    }

    .dataTables_length,
    .dataTables_filter {
        margin-bottom: 16px;
    }

    .dataTables_filter input {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 14px;
    }

    .dataTables_length select {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 14px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead th {
        background: #f9fafb;
        color: #374151;
        font-weight: 600;
        font-size: 13px;
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
    }

    tbody td {
        padding: 14px 12px;
        font-size: 14px;
        color: #1f2937;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    tbody tr:hover {
        background: #f9fbff;
    }

    /* ===== ACTION BUTTONS ===== */
    .action-group {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .btn-edit {
        background: #eaf2ff;
        color: #1363C6;
        font-size: 13px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
    }

    .btn-edit:hover {
        background: #dbeafe;
    }

    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
        font-size: 13px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
    }

    .btn-delete:hover {
        background: #fecaca;
    }

    /* ===== PAGINATION ===== */
    .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        padding: 6px 12px !important;
        margin: 0 2px;
    }

    .dataTables_paginate .paginate_button.current {
        background: #1363C6 !important;
        color: #ffffff !important;
    }

    /* ===== MOBILE ===== */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .page-container {
            padding: 20px 16px;
        }
    }
</style>

<div class="page-container">

    <!-- HEADER -->
    <div class="page-header">
        <h2>Hospitals</h2>
        <a href="{{ route('super_admin.hospitals_add_form') }}" class="add-hospital-btn">
            + Add Hospital
        </a>
    </div>

    <!-- CONTENT -->
    <div class="content-card">

        <div class="table-responsive">
            <table id="hospitalsTable" class="table datatable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Hospital Name</th>
                        <th>Hospital Phone</th>
                        <th>City</th>
                        <th>Admin Phone</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($hospitals as $hospital)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $hospital->hospital_name }}</td>
                            <td>{{ $hospital->hospital_phone }}</td>
                            <td>{{ $hospital->city }}</td>
                            <td>{{ $hospital->admin_phone }}</td>
                            <td class="text-center">
                                <div class="action-group">
                                    <a href="{{ route('super_admin.hospitals_edit_view',$hospital->id) }}"
                                       class="btn-edit">
                                        Edit
                                    </a>
                                    <a href="javascript:void(0)"
                                       class="btn-delete"
                                       onclick="deletefn(this)"
                                       data-id="{{ $hospital->id }}"
                                       data-action="{{ route('super_admin.hospital_delete') }}"
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

@push('scripts')
@endpush

@endsection
