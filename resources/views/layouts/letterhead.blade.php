<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Surat Resmi' }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm 2cm 2cm 2cm;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            color: #000;
            max-width: 100%;
            box-sizing: border-box;
        }
        
        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            display: table;
            width: 100%;
        }
        
        .logo-section {
            display: table-cell;
            width: 16.67%;
            vertical-align: middle;
            text-align: center;
        }
        
        .logo {
            width: 80px;
            height: 80px;
        }
        
        .kop-text {
            display: table-cell;
            width: 83.33%;
            vertical-align: middle;
            text-align: center;
            padding-left: 10px;
        }
        
        .kop-text h1 {
            font-size: 14px;
            font-weight: bold;
            margin: 2px 0;
            line-height: 1.1;
        }
        
        .kop-text h2 {
            font-size: 12px;
            font-weight: bold;
            margin: 2px 0;
            line-height: 1.1;
        }
        
        .kop-text h3 {
            font-size: 11px;
            font-weight: bold;
            margin: 2px 0;
            line-height: 1.1;
        }
        
        .contact-info {
            font-size: 9px;
            margin-top: 3px;
            font-style: normal;
            line-height: 1.2;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .letter-date {
            text-align: right;
            margin: 30px 0;
            font-size: 12px;
        }
        
        .letter-content {
            margin: 30px 0;
            text-align: justify;
            font-size: 12px;
            line-height: 1.6;
        }
        
        .letter-header-info {
            margin: 20px 0;
            font-size: 12px;
        }
        
        .letter-header-info table {
            border: none;
            width: 100%;
        }
        
        .letter-header-info td {
            padding: 2px 0;
            vertical-align: top;
        }
        
        .letter-header-info .label {
            width: 120px;
        }
        
        .letter-header-info .separator {
            width: 20px;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 50px;
            text-align: right;
            font-size: 12px;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
        }
        
        .signature-space {
            height: 60px;
            width: 150px;
            margin: 15px 0;
        }
        
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            table-layout: fixed;
            word-wrap: break-word;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 6px 4px;
            text-align: left;
            font-size: 10px;
            vertical-align: top;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                max-width: 100%;
                overflow: hidden;
            }
            
            table {
                page-break-inside: avoid;
            }
            
            .header {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- KOP SURAT -->
    <div class="header">
        <div class="logo-section">
            <img src="{{ public_path('assets/images/logos/logo.png') }}" alt="Logo Madrasah" class="logo">
        </div>
        
        <div class="kop-text">
            <h1>KEMENTERIAN AGAMA REPUBLIK INDONESIA</h1>
            <h2>KANTOR KEMENTERIAN AGAMA KABUPATEN HULU SUNGAI TENGAH</h2>
            <h3>MADRASAH ALIYAH NEGERI 1 HULU SUNGAI TENGAH</h3>
            <div class="contact-info">
                Alamat : Jalan H. Damanhuri Komp. Mesjid Agung Barabai Telp. (0517)41308<br>
                Website : man1hst.sch.id / e-mail : man1barabai@hoo.co.id , man1barabai@kemenag.go.id
            </div>
        </div>
    </div>

    <!-- TANGGAL SURAT -->
    <div class="letter-date">
        {{ $date ?? 'Barabai, ' . \Carbon\Carbon::now()->format('d F Y') }}<br>
        <strong>{{ $letterType ?? 'Kepala Madrasah' }}</strong>
    </div>

    <!-- ISI SURAT (CONTENT) -->
    <div class="letter-content">
        @yield('content')
    </div>

    <!-- TANDA TANGAN -->
    @if(!isset($hideSignature) || !$hideSignature)
    <div class="signature-section">
        <div class="signature-box">
            <br><br>
            <strong>{{ $signatureName ?? 'Kepala Madrasah' }}</strong><br>
            <div style="border-bottom: 1px solid #000; width: 150px; margin: 60px auto 0;"></div>
            <div style="margin-top: 5px;">{{ $signatureTitle ?? 'Soneran' }}</div>
        </div>
    </div>
    @endif
</body>
</html>