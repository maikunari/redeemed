<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Download Cards - Front</title>
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
            justify-content: space-between;
        }
        
        .card-header {
            text-align: center;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        
        .brand-name {
            font-size: 14pt;
            font-weight: bold;
            color: #1f2937;
            margin: 0;
            letter-spacing: 0.5px;
        }
        
        .tagline {
            font-size: 7pt;
            color: #6b7280;
            margin: 2px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
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
        
        /* Cut guides */
        .cut-guide {
            position: absolute;
            background: #000;
        }
        
        .cut-guide.horizontal {
            width: 0.125in;
            height: 1px;
        }
        
        .cut-guide.vertical {
            width: 1px;
            height: 0.125in;
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