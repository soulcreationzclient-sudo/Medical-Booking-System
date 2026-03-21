@extends('layouts.app1')
@section('title', 'Medicines')
@section('content')

<style>
    .med-hero {
        background: linear-gradient(135deg, #1363C6 0%, #0a3d8f 100%);
        padding: 32px 0 60px;
        margin-bottom: -36px;
    }
    .med-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .med-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .med-card-header h6 {
        font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;
    }
    .med-body { padding: 24px; }
    .fl {
        font-size: 12px; font-weight: 600; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 5px;
    }
    .fi {
        width: 100%; padding: 10px 12px;
        border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-size: 14px; color: #1e293b; background: #f8fafc;
        outline: none; transition: all 0.2s; font-family: inherit;
    }
    .fi:focus {
        border-color: #1363C6; background: #fff;
        box-shadow: 0 0 0 3px rgba(19,99,198,0.08);
    }
    .btn-add {
        background: #1363C6; color: #fff; border: none;
        padding: 10px 28px; border-radius: 10px;
        font-size: 14px; font-weight: 600; cursor: pointer;
        transition: all 0.2s; white-space: nowrap;
    }
    .btn-add:hover { background: #0f52a8; }
    table.med-table { width: 100%; border-collapse: collapse; }
    table.med-table thead tr {
        background: #f8fafc; border-bottom: 2px solid #e2e8f0;
    }
    table.med-table th {
        padding: 12px 20px; text-align: left; font-size: 12px;
        font-weight: 700; color: #64748b; text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    table.med-table tbody tr { border-bottom: 1px solid #f1f5f9; }
    table.med-table tbody tr:last-child { border-bottom: none; }
    table.med-table tbody tr:hover { background: #f8fafc; }
    table.med-table td {
        padding: 14px 20px; font-size: 14px;
        color: #334155; vertical-align: middle;
    }
    .alert-success-box {
        background: #dcfce7; color: #166534;
        border: 1px solid #bbf7d0; border-radius: 10px;
        padding: 12px 18px; margin-bottom: 20px;
        font-size: 14px; font-weight: 600;
    }
    .alert-error-box {
        background: #fee2e2; color: #991b1b;
        border: 1px solid #fecaca; border-radius: 10px;
        padding: 12px 18px; margin-bottom: 20px; font-size: 14px;
    }
    .empty-state {
        text-align: center; padding: 40px; color: #94a3b8; font-size: 14px;
    }
</style>

<div class="med-hero">
    <div class="container">
        <div style="color:#fff">
            <div style="font-size:13px;opacity:0.7;margin-bottom:4px">Hospital Admin</div>
            <div style="font-size:26px;font-weight:700">💊 Medicines</div>
        </div>
    </div>
</div>

<div class="container pb-5">

    @if(session('success'))
        <div class="alert-success-box">✓ {{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error-box">
            @foreach($errors->all() as $error)
                <div>✕ {{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ADD FORM --}}
    <div class="med-card">
        <div class="med-card-header">
            <h6>➕ Add Medicine</h6>
        </div>
        <div class="med-body">
            <form method="POST" action="{{ route('hospital_admin.medicines.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:16px;align-items:end;">

                    <div>
                        <div class="fl">Medicine Name *</div>
                        <input type="text" name="name" class="fi"
                               placeholder="e.g. Paracetamol"
                               value="{{ old('name') }}" required>
                    </div>

                    <div>
                        <div class="fl">Dosage</div>
                        <input type="text" name="dosage" class="fi"
                               placeholder="e.g. 500mg"
                               value="{{ old('dosage') }}">
                    </div>

                    <div>
                        <div class="fl">Price (RM) *</div>
                        <input type="number" step="0.01" min="0"
                               name="price" class="fi"
                               placeholder="e.g. 5.00"
                               value="{{ old('price') }}" required>
                    </div>

                    <div>
                        <button type="submit" class="btn-add">Add</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- MEDICINES TABLE --}}
    <div class="med-card">
        <div class="med-card-header">
            <h6>📋 Medicines List</h6>
            <span style="background:#eff6ff;color:#1363C6;padding:3px 10px;
                         border-radius:20px;font-size:12px;font-weight:700;">
                {{ $medicines->count() }} total
            </span>
        </div>

        @if($medicines->count() > 0)
            <div style="overflow-x:auto">
                <table class="med-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Dosage</th>
                            <th>Price (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicines as $i => $medicine)
                            <tr>
                                <td style="color:#94a3b8;font-size:13px">{{ $i + 1 }}</td>
                                <td style="font-weight:600">{{ $medicine->name }}</td>
                                <td>{{ $medicine->dosage ?? '—' }}</td>
                                <td style="color:#1363C6;font-weight:600">
                                    RM {{ number_format($medicine->price, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div style="font-size:36px;margin-bottom:10px">💊</div>
                <div style="font-weight:600;color:#64748b;margin-bottom:4px">No medicines added yet</div>
                <div>Use the form above to add your first medicine</div>
            </div>
        @endif
    </div>

</div>
@endsection