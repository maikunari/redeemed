<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Download Cards - Back</title>
    <style>
        @page {
            margin: 0.25in;
            size: 8.5in 11in;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.2;
        }
        
        .page {
            width: 8in;
            height: 10.5in;
            position: relative;
        }
        
        .card-grid {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
            height: 100%;
        }
        
        .business-card {
            width: 3.5in;
            height: 2in;
            border: 1px solid #ddd;
            box-sizing: border-box;
            padding: 0.15in;
            margin: 0.125in;
            background: white;
            position: relative;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .qr-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
        }
        
        .qr-code {
            width: 1.4in;
            height: 1.4in;
            margin: 0 auto 8px auto;
            display: block;
        }
        
        .qr-instructions {
            font-size: 8pt;
            color: #374151;
            font-weight: 500;
            margin: 0 0 4px 0;
        }
        
        .qr-subtext {
            font-size: 6pt;
            color: #6b7280;
            margin: 0;
            line-height: 1.3;
        }
        
        .brand-footer {
            font-size: 6pt;
            color: #9ca3af;
            margin-top: auto;
            padding-top: 4px;
            border-top: 1px solid #f3f4f6;
            width: 100%;
            text-align: center;
        }
        
        /* Print helpers */
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .business-card { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="card-grid">
            @foreach($codes->chunk(8) as $chunk)
                @foreach($chunk as $codeData)
                    <div class="business-card">
                        <div class="qr-container">
                            <img src="data:image/png;base64,{{ $codeData['qr_code'] }}" 
                                 alt="QR Code for {{ $codeData['code'] }}" 
                                 class="qr-code">
                            
                            <div class="qr-instructions">Scan to Download</div>
                            <div class="qr-subtext">Or visit website and enter code</div>
                        </div>
                        
                        <div class="brand-footer">
                            {{ $app_name ?? 'Redeemed' }} - Digital Content Distribution
                        </div>
                    </div>
                @endforeach
                
                @if(!$loop->last)
                    </div>
                    <div style="page-break-before: always;"></div>
                    <div class="page">
                        <div class="card-grid">
                @endif
            @endforeach
        </div>
    </div>
</body>
</html> 