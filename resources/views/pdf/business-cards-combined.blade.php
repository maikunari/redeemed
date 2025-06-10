<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Download Cards - Combined</title>
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
        
        table.card-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0.25in;
            margin: 0;
        }
        
        td.card-cell {
            width: 3.5in;
            height: 2in;
            vertical-align: top;
            padding: 0;
        }
        
        .business-card {
            width: 100%;
            height: 2in;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            box-sizing: border-box;
            padding: 0.15in;
            background: white;
            position: relative;
        }
        
        /* Front Side Styles */
        .front-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px solid #3b82f6;
        }
        
        .front-card .card-content {
            text-align: center;
            margin: 8px 0;
        }
        
        .front-card .card-footer {
            text-align: center;
            border-top: 1px solid #e5e5e5;
            padding-top: 6px;
            margin-top: 8px;
        }
        
        .card-header {
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
            margin-bottom: 12px;
            background: rgba(59, 130, 246, 0.05);
            margin: -0.2in -0.2in 12px -0.2in;
            padding: 12px 0.2in 10px 0.2in;
            border-radius: 6px 6px 0 0;
        }
        
        .brand-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1e40af;
            margin: 0;
            letter-spacing: 1px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .tagline {
            font-size: 8pt;
            color: #3b82f6;
            margin: 4px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        
        .file-title {
            font-size: 9pt;
            font-weight: 600;
            color: #374151;
            margin: 0 0 8px 0;
            line-height: 1.3;
            max-height: 2.6em;
            overflow: hidden;
        }
        
        .download-code {
            font-size: 16pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #dc2626;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 4px;
            padding: 6px 8px;
            margin: 4px 0;
            letter-spacing: 1px;
        }
        
        .usage-info {
            font-size: 7pt;
            color: #6b7280;
            margin: 4px 0 0 0;
        }
        
        .card-footer {
            text-align: center;
            border-top: 1px solid #e5e5e5;
            padding-top: 6px;
            margin-top: 8px;
        }
        
        .website-url {
            font-size: 8pt;
            color: #4b5563;
            margin: 0;
            font-weight: 500;
        }
        
        .instructions {
            font-size: 7pt;
            color: #6b7280;
            margin: 2px 0 0 0;
            line-height: 1.2;
        }
        
        .expires-info {
            font-size: 6pt;
            color: #9ca3af;
            margin: 2px 0 0 0;
        }
        
        /* Back Side Styles */
        .back-card {
            text-align: center;
            border: 2px solid #16a34a;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }
        
        .back-card .qr-container {
            text-align: center;
            margin: 10px 0;
        }
        
        .back-card .brand-footer {
            position: absolute;
            bottom: 8px;
            left: 0;
            right: 0;
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
        
        .page-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 0.25in;
            padding: 0.2in 0;
            border-bottom: 2px solid #e5e5e5;
        }
        
        .card-labels {
            width: 100%;
            margin: 0 0 0.15in 0;
            font-size: 10pt;
            font-weight: 600;
            color: #6b7280;
        }
        
        .card-labels table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .card-labels td {
            width: 50%;
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
    <!-- FRONT AND BACK SIDES TOGETHER -->
    <div class="page">
        <div class="page-title">Business Cards - Front & Back Preview</div>
        
        <div class="card-labels">
            <table>
                <tr>
                    <td>← FRONT SIDE</td>
                    <td>BACK SIDE →</td>
                </tr>
            </table>
        </div>
        
        <table class="card-table">
            @foreach($codes->chunk(4) as $chunkIndex => $chunk)
                @foreach($chunk as $codeData)
                    <tr>
                        <!-- Front Card -->
                        <td class="card-cell">
                            <div class="business-card front-card">
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
                        </td>
                        
                        <!-- Back Card -->
                        <td class="card-cell">
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
                        </td>
                    </tr>
                @endforeach
                
                @if(!$loop->last)
                    </table>
                    <div style="page-break-before: always;"></div>
                    <div class="page">
                        <div class="page-title">Business Cards - Front & Back Preview (continued)</div>
                        
                        <div class="card-labels">
                            <table>
                                <tr>
                                    <td>← FRONT SIDE</td>
                                    <td>BACK SIDE →</td>
                                </tr>
                            </table>
                        </div>
                        
                        <table class="card-table">
                @endif
            @endforeach
        </table>
    </div>
</body>
</html> 