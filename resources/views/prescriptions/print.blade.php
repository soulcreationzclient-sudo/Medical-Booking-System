<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription — {{ $booking->patient_name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@400;600;700&family=Inter:wght@400;500;600&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            color: #1e293b;
        }

        .page {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 20mm 18mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 16px;
            border-bottom: 3px solid #1363C6;
            margin-bottom: 20px;
        }
        .clinic-name {
            font-family: 'Source Serif 4', serif;
            font-size: 22px;
            font-weight: 700;
            color: #1363C6;
        }
        .clinic-sub { font-size: 12px; color: #64748b; margin-top: 3px; }

        .rx-symbol {
            font-family: 'Source Serif 4', serif;
            font-size: 48px;
            color: #1363C6;
            line-height: 1;
            opacity: 0.15;
        }

        /* PATIENT INFO */
        .patient-block {
            background: #f8fafc;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
        }
        .pi-label { font-size: 10px; font-weight: 600; color: #94a3b8;
                    text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 3px; }
        .pi-value { font-size: 13px; font-weight: 600; color: #1e293b; }

        /* ALLERGY ALERT */
        .allergy-alert {
            background: #fff7ed;
            border: 1.5px solid #fed7aa;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #92400e;
        }
        .allergy-alert strong { display: block; margin-bottom: 3px; }

        /* PRESCRIPTION TABLE */
        .rx-title {
            font-family: 'Source Serif 4', serif;
            font-size: 16px;
            font-weight: 700;
            color: #1363C6;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .rx-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        table.rx-print-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
            font-size: 13px;
        }
        table.rx-print-table thead tr {
            background: #1363C6;
            color: #fff;
        }
        table.rx-print-table th {
            padding: 9px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        table.rx-print-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        table.rx-print-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .med-num {
            display: inline-flex;
            width: 22px; height: 22px;
            background: #eff6ff; color: #1363C6;
            border-radius: 50%;
            align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700;
        }
        .med-name { font-weight: 600; color: #1e293b; }
        .med-instruct { font-size: 11px; color: #64748b; margin-top: 3px; }

        /* FOOTER */
        .footer {
            margin-top: 40px;
            padding-top: 16px;
            border-top: 1px dashed #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer-left { font-size: 11px; color: #94a3b8; }
        .signature-block { text-align: center; }
        .signature-line {
            width: 160px;
            border-bottom: 1.5px solid #334155;
            margin-bottom: 6px;
        }
        .signature-label { font-size: 11px; color: #64748b; }
        .doctor-name { font-size: 13px; font-weight: 600; color: #1e293b; }

        .watermark {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 80px;
            font-family: 'Source Serif 4', serif;
            color: rgba(19,99,198,0.04);
            pointer-events: none;
            font-weight: 700;
        }

        /* PRINT BUTTON (hidden when printing) */
        .print-actions {
            text-align: center;
            padding: 16px;
            background: #1363C6;
        }
        .print-btn {
            background: #fff; color: #1363C6;
            border: none; padding: 10px 28px;
            border-radius: 8px; font-size: 14px;
            font-weight: 700; cursor: pointer;
            margin-right: 10px;
        }
        .close-btn {
            background: transparent; color: rgba(255,255,255,0.8);
            border: 1px solid rgba(255,255,255,0.4);
            padding: 10px 20px; border-radius: 8px;
            font-size: 14px; cursor: pointer;
        }

        @media print {
            body { background: #fff; }
            .print-actions { display: none; }
            .page {
                margin: 0; box-shadow: none;
                width: 100%; padding: 15mm 12mm;
            }
            .watermark { position: fixed; }
        }
    </style>
</head>
<body>

<div class="print-actions">
    <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
    <button class="close-btn" onclick="window.close()">✕ Close</button>
</div>

<div class="page">
    <div class="watermark">Rx</div>

    {{-- HEADER --}}
    <div class="header">
        <div>
            <div class="clinic-name">{{ $booking->hospital_name ?? config('app.name') }}</div>
            <div class="clinic-sub">Medical Prescription</div>
        </div>
        <div class="rx-symbol">Rx</div>
    </div>

    {{-- DATE + REF --}}
    <div style="display:flex;justify-content:space-between;margin-bottom:18px;font-size:12px;color:#64748b">
        <span>Date: <strong style="color:#1e293b">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</strong></span>
        <span>Ref: <strong style="color:#1363C6;font-family:monospace">{{ $booking->action_token }}</strong></span>
    </div>

    {{-- PATIENT INFO --}}
    <div class="patient-block">
        <div>
            <div class="pi-label">Patient Name</div>
            <div class="pi-value">{{ $booking->patient_name }}</div>
        </div>
        <div>
            <div class="pi-label">Phone</div>
            <div class="pi-value">{{ $booking->patient_phone }}</div>
        </div>
        <div>
            <div class="pi-label">Age</div>
            <div class="pi-value">{{ $booking->age ? $booking->age . ' years' : '—' }}</div>
        </div>
        @if($patient)
        <div>
            <div class="pi-label">Gender</div>
            <div class="pi-value">{{ $patient->gender ? ucfirst($patient->gender) : '—' }}</div>
        </div>
        <div>
            <div class="pi-label">Blood Type</div>
            <div class="pi-value">{{ $patient->blood_type ?? '—' }}</div>
        </div>
        <div>
            <div class="pi-label">IC / Passport</div>
            <div class="pi-value">{{ $patient->ic_passport_no ?? '—' }}</div>
        </div>
        @endif
    </div>

    {{-- COMPLAINT --}}
    @if($booking->cause)
    <div style="margin-bottom:20px;font-size:13px">
        <span style="font-weight:600;color:#475569">Chief Complaint: </span>
        <span style="color:#64748b">{{ $booking->cause }}</span>
    </div>
    @endif

    {{-- ALLERGY ALERT --}}
    @if($patient && !empty($patient->allergy))
    <div class="allergy-alert">
        <strong>⚠️ Known Allergy / Medical History</strong>
        {{ $patient->allergy }}
    </div>
    @endif

    {{-- PRESCRIPTIONS --}}
    <div class="rx-title">Prescribed Medications</div>

    @if($prescriptions->count() > 0)
        <table class="rx-print-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescriptions as $i => $rx)
                    <tr>
                        <td><span class="med-num">{{ $i + 1 }}</span></td>
                        <td>
                            <div class="med-name">{{ $rx->medicine_name }}</div>
                            @if($rx->instructions)
                                <div class="med-instruct">📝 {{ $rx->instructions }}</div>
                            @endif
                        </td>
                        <td>{{ $rx->dosage ?? '—' }}</td>
                        <td>{{ $rx->frequency ?? '—' }}</td>
                        <td>{{ $rx->duration ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align:center;color:#94a3b8;padding:20px;font-size:13px">No prescriptions recorded.</p>
    @endif

    {{-- FOOTER / SIGNATURE --}}
    <div class="footer">
        <div class="footer-left">
            <div>Issued: {{ now()->format('d M Y, h:i A') }}</div>
            <div style="margin-top:4px">{{ $booking->hospital_name ?? '' }}</div>
        </div>
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="doctor-name">{{ $booking->doctor_name ?? 'Attending Doctor' }}</div>
            <div class="signature-label">Signature & Stamp</div>
        </div>
    </div>

</div>

</body>
</html>