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
            font-family: 'Georgia', 'Times New Roman', serif;
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
            flex-direction: row;
            padding: 0;
            background: #c4a3a3;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        /* Front Side Styles */
        .front-card {
            background: linear-gradient(135deg, #d4b5b5 0%, #c4a3a3 100%);
        }
        
        .qr-section {
            width: 40%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.15in;
            position: relative;
        }
        
        .qr-code {
            width: 0.9in;
            height: 0.9in;
            margin-bottom: 8pt;
            border-radius: 4px;
            background: white;
            padding: 2px;
        }
        
        .website-url {
            font-size: 7pt;
            color: white;
            font-weight: 300;
            letter-spacing: 0.5pt;
            text-transform: uppercase;
            margin-top: auto;
        }
        
        .divider {
            width: 1px;
            height: 100%;
            background: white;
            opacity: 0.8;
        }
        
        .content-section {
            width: 60%;
            padding: 0.15in;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
            background: white;
        }
        
        .brand-name {
            font-size: 16pt;
            font-weight: normal;
            color: #2d2d2d;
            margin: 0 0 4pt 0;
            letter-spacing: 2pt;
            text-transform: uppercase;
            font-family: 'Georgia', serif;
        }
        
        .file-title {
            font-size: 9pt;
            color: #666;
            margin: 0 0 12pt 0;
            font-style: italic;
            font-family: 'Georgia', serif;
            font-weight: 300;
        }
        
        .download-code {
            font-size: 14pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #c4a3a3;
            background: #f8f8f8;
            border: 1pt solid #e0e0e0;
            border-radius: 4px;
            padding: 6pt 8pt;
            margin: 8pt 0;
            letter-spacing: 1pt;
            text-align: center;
        }
        
        .file-thumbnail {
            width: 60px;
            height: 60px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            object-fit: cover;
            margin: 8pt 0;
            border: 1pt solid #e0e0e0;
        }
        
        .usage-info {
            font-size: 7pt;
            color: #999;
            margin: 4pt 0 0 0;
            font-family: 'Arial', sans-serif;
        }
        
        /* Back Side Styles */
        .back-card {
            background: white;
            flex-direction: column;
            padding: 0.2in;
            justify-content: space-between;
        }
        
        .back-header {
            text-align: center;
            border-bottom: 1pt solid #e0e0e0;
            padding-bottom: 8pt;
            margin-bottom: 12pt;
        }
        
        .back-brand-name {
            font-size: 14pt;
            font-weight: normal;
            color: #c4a3a3;
            letter-spacing: 1.5pt;
            text-transform: uppercase;
            font-family: 'Georgia', serif;
        }
        
        .back-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        
        .download-instructions {
            font-size: 10pt;
            color: #2d2d2d;
            margin: 0 0 12pt 0;
            line-height: 1.4;
            font-family: 'Georgia', serif;
        }
        
        .download-code-back {
            font-size: 16pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #c4a3a3;
            background: #f8f8f8;
            border: 1pt solid #e0e0e0;
            border-radius: 4px;
            padding: 8pt;
            margin: 8pt 0;
            letter-spacing: 1pt;
        }
        
        .website-back {
            font-size: 9pt;
            color: #666;
            margin: 8pt 0;
            font-family: 'Arial', sans-serif;
        }
        
        .qr-instructions {
            font-size: 8pt;
            color: #999;
            margin: 4pt 0;
            font-style: italic;
            font-family: 'Georgia', serif;
        }
        
        .back-footer {
            text-align: center;
            border-top: 1pt solid #e0e0e0;
            padding-top: 8pt;
            margin-top: auto;
        }
        
        .brand-footer {
            font-size: 7pt;
            color: #999;
            font-family: 'Georgia', serif;
            font-style: italic;
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
                    <div class="business-card front-card">
                        <div class="qr-section">
                            <img src="data:image/svg+xml;base64,{{ $codeData['qr_code'] }}" 
                                 alt="QR Code for {{ $codeData['code'] }}" 
                                 class="qr-code">
                            <div class="website-url">{{ parse_url($website_url ?? config('app.url'), PHP_URL_HOST) }}</div>
                        </div>
                        
                        <div class="divider"></div>
                        
                        <div class="content-section">
                            <div class="brand-name">{{ $brand_name ?? 'Redeemed' }}</div>
                            @if($codeData['file_title'])
                                <div class="file-title">{{ $codeData['file_title'] }}</div>
                            @endif
                            
                            @if(isset($codeData['file_thumbnail']) && $codeData['file_thumbnail'])
                                <img src="{{ $codeData['file_thumbnail'] }}" 
                                     alt="File thumbnail" 
                                     class="file-thumbnail">
                            @endif
                            
                            <div class="download-code">{{ $codeData['code'] }}</div>
                            <div class="usage-info">{{ $codeData['usage_info'] }}</div>
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
                            <div class="back-brand-name">{{ $brand_name ?? 'Redeemed' }}</div>
                        </div>
                        
                        <div class="back-content">
                            <div class="download-instructions">{{ $card_instructions ?? 'Visit the website below and enter your download code to access your digital content.' }}</div>
                            
                            <div class="download-code-back">{{ $codeData['code'] }}</div>
                            
                            <div class="website-back">{{ parse_url($website_url ?? config('app.url'), PHP_URL_HOST) }}/redeem</div>
                            
                            <div class="qr-instructions">{{ $qr_instruction ?? 'Or scan the QR code on the front' }}</div>
                        </div>
                        
                        <div class="back-footer">
                            <div class="brand-footer">{{ $brand_name ?? 'Redeemed' }} - Digital Content</div>
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