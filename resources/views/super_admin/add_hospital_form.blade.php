@extends('layouts.app1')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    body {
        background: #f4f7fb;
        font-family: 'Open Sans', sans-serif;
    }

    /* Center wrapper */
    .form-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    /* Card – WIDER, SAME LOGIN STYLE */
    .form-card {
        width: 100%;
        max-width: 900px; /* 🔥 increased width */
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        padding: 50px 48px; /* slightly wider padding */
        box-sizing: border-box;
    }

    /* Header */
    .form-card h1 {
        margin: 0 0 6px 0;
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
    }

    .form-card p {
        margin: 0 0 30px 0;
        font-size: 14px;
        color: #6b7280;
    }

    /* ===== x-form overrides (MATCH LOGIN INPUTS) ===== */

    .form-card label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .form-card input,
    .form-card select,
    .form-card textarea {
        width: 100%;
        padding: 14px;
        font-size: 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        box-sizing: border-box;
        margin-bottom: 22px;
        background: #ffffff;
    }

    .form-card input:focus,
    .form-card select:focus,
    .form-card textarea:focus {
        outline: none;
        border-color: #1363C6;
        box-shadow: 0 0 0 3px rgba(19,99,198,0.15);
    }

    /* Validation */
    .form-card .invalid-feedback,
    .form-card .text-danger {
        font-size: 12px;
        color: #dc2626;
        margin-top: -16px;
        margin-bottom: 14px;
    }

    /* Buttons */
    .form-card button[type="submit"],
    .form-card button[type="reset"] {
        width: 100%;
        padding: 14px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        margin-top: 10px;
    }

    .form-card button[type="submit"] {
        background: #1363C6;
        color: #ffffff;
    }

    .form-card button[type="submit"]:hover {
        background: #0f52a5;
    }

    .form-card button[type="reset"] {
        background: #f1f5f9;
        color: #374151;
    }

    /* =========================
   FINAL FIX: DB STATUS ROW
   ========================= */

/* Do NOT force radios full width */
.form-card input[type="radio"] {
    width: auto !important;
    margin: 0;
}

/* Radio container */
.form-card .radio-group,
.form-card .form-radio-group {
    display: inline-flex !important;
    align-items: center;
    gap: 32px;
    margin-bottom: 22px;
    min-width: 280px;
}

/* Radio label inline */
.form-card .radio-group label,
.form-card .form-radio-group label {
    display: inline-flex !important;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    white-space: nowrap;
}

/* Prevent word breaking */
.form-card label {
    word-break: normal !important;
    white-space: normal;
}

/* =========================
   FILE INPUT FIX
   ========================= */
.form-card input[type="file"] {
    padding: 10px 12px;
}

/* =========================
   ROW LAYOUT FIX
   ========================= */
.form-card .row {
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    align-items: flex-start;
}

.form-card .row > div {
    flex: 1 1 48%;
}

.form-card .row > .col-12 {
    flex: 0 0 100%;
}



    /* Mobile */
    @media (max-width: 768px) {
        .form-card {
            padding: 40px 24px;
            max-width: 100%;
        }
    }
</style>

<div class="form-page">
    <div class="form-card">

        <h1>Hospital Form</h1>
        <p>Please fill in the hospital details</p>

        {{-- FORM COMPONENT (UNCHANGED) --}}
        <x-form
            :fields="$fields"
            :action="$action"
            :method="$method"
            :model="$model"
            submit="{{ $submit }}"
            :showReset="true"
        />

    </div>
</div>

@endsection
