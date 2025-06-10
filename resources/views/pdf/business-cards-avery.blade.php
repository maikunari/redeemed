<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Business Cards - Avery 5371 Template</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: 8.5in 11in;
            margin: 0;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            background: white;
            width: 8.5in;
            height: 11in;
            margin: 0 auto;
            position: relative;
        }
        
        .page {
            width: 8.5in;
            height: 11in;
            position: relative;
            padding-top: 0.5in;
            padding-left: 0.75in;
            padding-right: 0.75in;
        }
        
        .card-grid {
            display: grid;
            grid-template-columns: repeat(2, 3.5in);
            grid-template-rows: repeat(5, 2in);
            gap: 0;
            width: 100%;
            height: 10in;
        }
        
        .business-card {
            width: 3.5in;
            height: 2in;
            position: relative;
            display: flex;
            flex-direction: column;
            padding: 0.15in;
            background: white;
            overflow: hidden;
            border: 0.5pt solid #e0e0e0;
        }
        
        /* Card Styles */
        .card-header {
            text-align: center;
            border-bottom: 1pt solid #000000;
            padding: 4pt 0 4pt 0;
            margin-bottom: 6pt;
        }
        
        .brand-name {
            font-size: 14pt;
            font-weight: bold;
            color: #000000;
            margin: 0;
            letter-spacing: 0.5pt;
        }
        
        .tagline {
            font-size: 8pt;
            color: #000000;
            margin: 2pt 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            font-weight: normal;
        }
        
        .card-content {
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 4pt 0;
        }
        
        .file-title {
            font-size: 9pt;
            font-weight: bold;
            color: #000000;
            margin: 0 0 4pt 0;
            line-height: 1.2;
        }
        
        .download-code {
            font-size: 16pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #000000;
            border: 1pt solid #000000;
            padding: 4pt 6pt;
            margin: 4pt 0;
            letter-spacing: 1pt;
            display: inline-block;
        }
        
        .usage-info {
            font-size: 7pt;
            color: #000000;
            margin: 2pt 0 0 0;
        }
        
        .card-footer {
            text-align: center;
            border-top: 0.5pt solid #000000;
            padding-top: 3pt;
            margin-top: auto;
        }
        
        .website-url {
            font-size: 8pt;
            color: #000000;
            margin: 0;
            font-weight: bold;
        }
        
        .instructions {
            font-size: 6pt;
            color: #000000;
            margin: 1pt 0 0 0;
        }
        
        .expires-info {
            font-size: 6pt;
            color: #000000;
            margin: 1pt 0 0 0;
        }
        
        /* QR Code Back Side */
        .back-card {
            background: white;
            justify-content: flex-start;
        }
        
        .back-header {
            text-align: center;
            border-bottom: 1pt solid #000000;
            padding: 4pt 0 4pt 0;
            margin-bottom: 8pt;
        }
        
        .back-content {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 6pt;
            border: 1pt solid #000000;
            margin: 0 0 8pt 0;
        }
        
        .qr-section {
            width: 45%;
            text-align: center;
        }
        
        .qr-code {
            width: 0.9in;
            height: 0.9in;
            margin: 0 auto;
            display: block;
        }
        
        .info-section {
            width: 55%;
            padding-left: 8pt;
            text-align: center;
        }
        
        .download-code-back {
            font-size: 14pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #000000;
            margin: 0 0 4pt 0;
            letter-spacing: 0.5pt;
        }
        
        .website-back {
            font-size: 8pt;
            color: #000000;
            margin: 0;
            font-weight: bold;
        }
        
        .qr-instructions {
            font-size: 7pt;
            color: #000000;
            font-weight: bold;
            margin: 2pt 0 0 0;
        }
        
        .brand-footer {
            text-align: center;
            font-size: 6pt;
            color: #000000;
            padding-top: 3pt;
            border-top: 0.5pt solid #000000;
        }
        

        
        /* Print helpers */
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .business-card { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- FRONT SIDES PAGE -->
    <div class="page">
        <div class="card-grid">
            @foreach($codes->chunk(10) as $pageChunk)
                @foreach($pageChunk as $codeData)
                    <div class="business-card">
                                <div class="card-header">
            <div class="brand-name">{{ $brand_name ?? 'Redeemed' }}</div>
            <div class="tagline">Digital Content Download</div>
        </div>
                        
                        <div class="card-content">
                            @if($codeData['file_title'])
                                <div class="file-title">{{ $codeData['file_title'] }}</div>
                            @endif
                            
                            <div class="download-code">{{ $codeData['code'] }}</div>
                            
                            <div class="usage-info">{{ $codeData['usage_info'] }}</div>
                        </div>
                        
                        <div class="card-footer">
                                        <div class="website-url">{{ parse_url($website_url ?? config('app.url'), PHP_URL_HOST) }}/redeem</div>
            <div class="instructions">{{ $card_instructions ?? 'Enter code above to download' }}</div>
                            @if($codeData['expires_at'])
                                <div class="expires-info">Expires: {{ $codeData['expires_at'] }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
                
                @if(!$loop->last)
                    </div>
                    </div>
                    <div style="page-break-before: always;"></div>
                    <div class="page">
                        <div class="card-grid">
                @endif
            @endforeach
        </div>
    </div>

    <!-- PAGE BREAK -->
    <div style="page-break-before: always;"></div>

    <!-- BACK SIDES PAGE -->
    <div class="page">
        <div class="card-grid">
            @foreach($codes->chunk(10) as $pageChunk)
                @foreach($pageChunk as $codeData)
                    <div class="business-card back-card">
                        <div class="back-header">
                            <div class="brand-name">{{ $brand_name ?? 'Redeemed' }}</div>
                        </div>
                        
                        <div class="back-content">
                            <div class="qr-section">
                                <img src="data:image/svg+xml;base64,{{ $codeData['qr_code'] }}" 
                                     alt="QR Code for {{ $codeData['code'] }}" 
                                     class="qr-code">
                                <div class="qr-instructions">{{ $qr_instruction ?? 'Scan to Download' }}</div>
                            </div>
                            
                            <div class="info-section">
                                <div class="download-code-back">{{ $codeData['code'] }}</div>
                                <div class="website-back">{{ parse_url($website_url ?? config('app.url'), PHP_URL_HOST) }}/redeem</div>
                            </div>
                        </div>
                        
                        <div class="brand-footer">
                            {{ $brand_name ?? 'Redeemed' }}
                        </div>
                    </div>
                @endforeach
                
                @if(!$loop->last)
                    </div>
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