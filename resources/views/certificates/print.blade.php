<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sertifikat - {{ $certificate->certificate_no }}</title>
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px;
            background: #e9ecef;
            color: #1f2937;
            font-family: Georgia, 'Times New Roman', serif;
        }

        .toolbar {
            max-width: 1120px;
            margin: 0 auto 16px;
            text-align: right;
            font-family: Arial, sans-serif;
        }

        .print-button {
            border: 0;
            border-radius: 6px;
            background: #2563eb;
            color: #fff;
            cursor: pointer;
            padding: 10px 16px;
            font-weight: 600;
        }

        .certificate {
            position: relative;
            max-width: 1120px;
            min-height: 760px;
            margin: 0 auto;
            padding: 56px;
            background: #fffdf7;
            border: 14px solid #b88928;
            outline: 4px solid #f7d77a;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .18);
            overflow: hidden;
        }

        .certificate::before,
        .certificate::after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(184, 137, 40, .08);
        }

        .certificate::before {
            top: -90px;
            left: -90px;
        }

        .certificate::after {
            right: -90px;
            bottom: -90px;
        }

        .content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .eyebrow {
            color: #9a6a12;
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 5px;
            margin-bottom: 16px;
            text-transform: uppercase;
        }

        h1 {
            color: #111827;
            font-size: 58px;
            letter-spacing: 3px;
            margin: 0;
            text-transform: uppercase;
        }

        .subtitle {
            color: #4b5563;
            font-family: Arial, sans-serif;
            font-size: 20px;
            margin: 18px 0 48px;
        }

        .student-name {
            border-bottom: 2px solid #b88928;
            color: #111827;
            display: inline-block;
            font-size: 44px;
            font-weight: 700;
            margin-bottom: 26px;
            min-width: 520px;
            padding: 0 32px 12px;
        }

        .statement {
            color: #374151;
            font-family: Arial, sans-serif;
            font-size: 20px;
            line-height: 1.7;
            margin: 0 auto;
            max-width: 780px;
        }

        .course-title {
            color: #9a6a12;
            font-size: 28px;
            font-weight: 700;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            gap: 32px;
            margin-top: 70px;
            text-align: left;
        }

        .meta-box {
            flex: 1;
            font-family: Arial, sans-serif;
            font-size: 15px;
        }

        .signature {
            text-align: center;
        }

        .signature-image {
            height: 82px;
            margin: 0 auto -8px;
            max-width: 260px;
            object-fit: contain;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            margin: 12px auto 8px;
            width: 260px;
        }

        .certificate-no {
            bottom: 24px;
            color: #6b7280;
            font-family: Arial, sans-serif;
            font-size: 13px;
            left: 0;
            position: absolute;
            right: 0;
            text-align: center;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            body {
                background: #fff;
                padding: 0;
            }

            .toolbar {
                display: none;
            }

            .certificate {
                border-width: 10px;
                box-shadow: none;
                max-width: none;
                min-height: calc(100vh - 20mm);
                outline-width: 3px;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    @php
        $signerName = $certificate->signer_name
            ?? $certificate->signer?->name
            ?? $certificate->course->certificateSigner?->name
            ?? 'Belum diatur';
        $signerPosition = $certificate->signer_position
            ?? $certificate->signer?->position
            ?? $certificate->course->certificateSigner?->position
            ?? 'Penandatangan';
        $signaturePath = $certificate->signature_path
            ?? $certificate->signer?->signature_path
            ?? $certificate->course->certificateSigner?->signature_path;
    @endphp

    <div class="toolbar">
        <button class="print-button" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak Sertifikat
        </button>
    </div>

    <main class="certificate">
        <div class="content">
            <div class="eyebrow">E-Learning Certificate</div>
            <h1>Sertifikat</h1>
            <div class="subtitle">Diberikan sebagai bukti kelulusan pembelajaran kepada</div>

            <div class="student-name">{{ $certificate->student->name }}</div>

            <p class="statement">
                Telah menyelesaikan seluruh materi pembelajaran pada kelas
                <br>
                <span class="course-title">{{ $certificate->course->title }}</span>
                <br>
                dan dinyatakan memenuhi syarat kelulusan.
            </p>

            <div class="meta">
                <div class="meta-box">
                    <strong>Tanggal Terbit</strong><br>
                    {{ $certificate->issued_at?->format('d F Y') ?? '-' }}
                    <br><br>
                    <strong>Nomor Sertifikat</strong><br>
                    {{ $certificate->certificate_no }}
                </div>
                <div class="meta-box signature">
                    @if($signaturePath)
                        <img src="{{ asset('storage/' . $signaturePath) }}"
                             alt="TTD {{ $signerName }}"
                             class="signature-image">
                    @endif
                    <div class="signature-line"></div>
                    <strong>{{ $signerName }}</strong><br>
                    {{ $signerPosition }}
                </div>
            </div>
        </div>

        <div class="certificate-no">
            Validasi: {{ $certificate->certificate_no }}
        </div>
    </main>
</body>
</html>
