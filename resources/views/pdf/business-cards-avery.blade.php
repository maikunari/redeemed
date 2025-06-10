<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Business Cards - Avery 5371 Template</title>
    <style>
        @page {
            margin: 0.5in 0.1875in 0.5in 0.1875in;
            size: 8.5in 11in;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.2;
        }
        
        /* Avery 5371 Template - 10 cards per sheet */
        .avery-sheet {
            width: 8.125in;
            height: 10in;
        }
        
        .card-row {
            height: 2in;
            margin-bottom: 0in;
            display: block;
            page-break-inside: avoid;
        }
        
        .card-row:not(:last-child) {
            margin-bottom: 0in;
        }
        
        .business-card {
            width: 3.5in;
            height: 2in;
            float: left;
            margin-right: 1.125in;
            margin-bottom: 0;
            border: 1px solid #ddd;
            box-sizing: border-box;
            padding: 0.125in;
            background: white;
            position: relative;
        }
        
        .business-card:nth-child(2n) {
            margin-right: 0;
        }
        
        .business-card:after {
            content: "";
            display: table;
            clear: both;
        }
        
        /* Card Styles */
        .card-header {
            text-align: center;
            border-bottom: 1px solid #3b82f6;
            padding-bottom: 4px;
            margin-bottom: 6px;
            background: rgba(59, 130, 246, 0.05);
            margin: -0.125in -0.125in 6px -0.125in;
            padding: 8px 0.125in 4px 0.125in;
        }
        
        .brand-name {
            font-size: 14pt;
            font-weight: bold;
            color: #1e40af;
            margin: 0;
            letter-spacing: 0.5px;
        }
        
        .tagline {
            font-size: 6pt;
            color: #3b82f6;
            margin: 2px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 600;
        }
        
        .card-content {
            text-align: center;
            margin: 6px 0;
        }
        
        .file-title {
            font-size: 8pt;
            font-weight: 600;
            color: #374151;
            margin: 0 0 4px 0;
            line-height: 1.2;
            max-height: 2em;
            overflow: hidden;
        }
        
        .download-code {
            font-size: 12pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #dc2626;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 3px;
            padding: 3px 6px;
            margin: 3px 0;
            letter-spacing: 0.5px;
        }
        
        .usage-info {
            font-size: 6pt;
            color: #6b7280;
            margin: 3px 0 0 0;
        }
        
        .card-footer {
            position: absolute;
            bottom: 0.125in;
            left: 0.125in;
            right: 0.125in;
            text-align: center;
            border-top: 1px solid #e5e5e5;
            padding-top: 3px;
        }
        
        .website-url {
            font-size: 7pt;
            color: #4b5563;
            margin: 0;
            font-weight: 500;
        }
        
        .instructions {
            font-size: 5pt;
            color: #6b7280;
            margin: 1px 0 0 0;
        }
        
        .expires-info {
            font-size: 5pt;
            color: #9ca3af;
            margin: 1px 0 0 0;
        }
        
        /* QR Code Back Side */
        .back-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #16a34a;
        }
        
        .qr-container {
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
        }
        
        .qr-code {
            width: 1.2in;
            height: 1.2in;
            margin: 0 auto 4px auto;
            display: block;
        }
        
        .qr-instructions {
            font-size: 7pt;
            color: #374151;
            font-weight: 600;
            margin: 0 0 2px 0;
        }
        
        .qr-subtext {
            font-size: 5pt;
            color: #6b7280;
            margin: 0;
        }
        
        .brand-footer {
            position: absolute;
            bottom: 4px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 5pt;
            color: #9ca3af;
        }
        
        .page-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 0.25in;
            padding-bottom: 0.125in;
            border-bottom: 2px solid #e5e5e5;
        }
        
        .template-info {
            text-align: center;
            font-size: 8pt;
            color: #6b7280;
            margin-bottom: 0.25in;
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
    <div class="page-title">Business Cards - Front Side (Avery 5371)</div>
    <div class="template-info">Print on Avery 5371 perforated business card stock</div>
    
    <div class="avery-sheet">
        @foreach($codes->chunk(10) as $pageChunk)
            @foreach($pageChunk->chunk(2) as $rowChunk)
                <div class="card-row">
                    @foreach($rowChunk as $codeData)
                        <div class="business-card">
                            <div class="card-header">
                                <h1 class="brand-name">{{ $app_name ?? 'Redeemed' }}</h1>
                                <p class="tagline">Digital Content Download</p>
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
                                <div class="instructions">Enter code above to download</div>
                                @if($codeData['expires_at'])
                                    <div class="expires-info">Expires: {{ $codeData['expires_at'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
            
            @if(!$loop->last)
                <div style="page-break-before: always;"></div>
                <div class="page-title">Business Cards - Front Side (continued)</div>
                <div class="template-info">Print on Avery 5371 perforated business card stock</div>
                <div class="avery-sheet">
            @endif
        @endforeach
    </div>

    <!-- PAGE BREAK -->
    <div style="page-break-before: always;"></div>

    <!-- BACK SIDES PAGE -->
    <div class="page-title">Business Cards - Back Side (QR Codes)</div>
    <div class="template-info">Print on REVERSE side of Avery 5371 cards</div>
    
    <div class="avery-sheet">
        @foreach($codes->chunk(10) as $pageChunk)
            @foreach($pageChunk->chunk(2) as $rowChunk)
                <div class="card-row">
                    @foreach($rowChunk as $codeData)
                        <div class="business-card back-card">
                            <div class="qr-container">
                                <img src="data:image/svg+xml;base64,{{ $codeData['qr_code'] }}" 
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
                </div>
            @endforeach
            
            @if(!$loop->last)
                <div style="page-break-before: always;"></div>
                <div class="page-title">Business Cards - Back Side (continued)</div>
                <div class="template-info">Print on REVERSE side of Avery 5371 cards</div>
                <div class="avery-sheet">
            @endif
        @endforeach
    </div>
</body>
</html> 