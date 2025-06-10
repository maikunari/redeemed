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
            margin: 0.5in 0.75in;
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
            margin: 0;
            padding: 0;
        }
        
        .page {
            width: 100%;
            position: relative;
        }
        
        /* Table-based layout for DomPDF compatibility */
        .card-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }
        
        .card-row {
            page-break-inside: avoid;
        }
        
        .card-cell {
            width: 3.5in;
            height: 2in;
            vertical-align: top;
            padding: 0.125in;
        }
        
        .business-card {
            width: 100%;
            height: 100%;
            position: relative;
            background: #d4b5b5;
            background: linear-gradient(135deg, #d4b5b5 0%, #c4a3a3 100%);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        /* Front Side Layout - Table for left/right sections */
        .front-card {
            display: table;
            width: 100%;
            height: 100%;
        }
        
        .qr-section {
            display: table-cell;
            width: 40%;
            vertical-align: middle;
            text-align: center;
            padding: 0.1in;
            position: relative;
        }
        
        .qr-code {
            width: 0.9in;
            height: 0.9in;
            margin: 0 auto 8pt auto;
            border-radius: 4px;
            background: white;
            padding: 2px;
            display: block;
        }
        
        .website-url {
            font-size: 7pt;
            color: white;
            font-weight: 300;
            letter-spacing: 0.5pt;
            text-transform: uppercase;
            position: absolute;
            bottom: 0.1in;
            left: 0;
            right: 0;
            text-align: center;
        }
        
        .divider {
            display: table-cell;
            width: 1px;
            background: white;
            opacity: 0.8;
            vertical-align: top;
            padding: 0;
        }
        
        .content-section {
            display: table-cell;
            width: 59%;
            vertical-align: middle;
            padding: 0.15in;
            background: white;
            position: relative;
        }
        
        .brand-name {
            font-size: 16pt;
            font-weight: normal;
            color: #2d2d2d;
            margin: 0 0 4pt 0;
            letter-spacing: 2pt;
            text-transform: uppercase;
            font-family: 'Georgia', serif;
            line-height: 1.1;
        }
        
        .file-title {
            font-size: 9pt;
            color: #666;
            margin: 0 0 8pt 0;
            font-style: italic;
            font-family: 'Georgia', serif;
            font-weight: 300;
            line-height: 1.2;
        }
        
        .file-thumbnail {
            width: 50px;
            height: 50px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin: 6pt 0;
            border: 1pt solid #e0e0e0;
            display: block;
        }
        
        .download-code {
            font-size: 13pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #c4a3a3;
            background: #f8f8f8;
            border: 1pt solid #e0e0e0;
            border-radius: 4px;
            padding: 6pt 8pt;
            margin: 6pt 0;
            letter-spacing: 1pt;
            text-align: center;
            line-height: 1;
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
            padding: 0.2in;
            text-align: center;
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
            margin: 10pt 0;
        }
        
        .download-instructions {
            font-size: 9pt;
            color: #2d2d2d;
            margin: 0 0 10pt 0;
            line-height: 1.3;
            font-family: 'Georgia', serif;
        }
        
        .download-code-back {
            font-size: 15pt;
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
            margin-top: 12pt;
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
            .card-row { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- FRONT SIDES PAGE -->
    <div class="page">
        <table class="card-table">
            @foreach($codes->chunk(10) as $pageChunk)
                @foreach($pageChunk->chunk(2) as $rowChunk)
                    <tr class="card-row">
                        @foreach($rowChunk as $codeData)
                            <td class="card-cell">
                                <div class="business-card">
                                    <div class="front-card">
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
                                            
                                            {{-- Thumbnail temporarily disabled for PDF generation performance --}}
                                            {{-- @if(isset($codeData['file_thumbnail']) && $codeData['file_thumbnail'])
                                                <img src="{{ $codeData['file_thumbnail'] }}" 
                                                     alt="File thumbnail" 
                                                     class="file-thumbnail">
                                            @endif --}}
                                            
                                            <div class="download-code">{{ $codeData['code'] }}</div>
                                            <div class="usage-info">{{ $codeData['usage_info'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        @endforeach
                        
                        @if($rowChunk->count() == 1)
                            <td class="card-cell"></td>
                        @endif
                    </tr>
                @endforeach
                
                @if(!$loop->last)
                    </table>
                    <div style="page-break-before: always;"></div>
                    <div class="page">
                        <table class="card-table">
                @endif
            @endforeach
        </table>
    </div>

    <!-- PAGE BREAK -->
    <div style="page-break-before: always;"></div>

    <!-- BACK SIDES PAGE -->
    <div class="page">
        <table class="card-table">
            @foreach($codes->chunk(10) as $pageChunk)
                @foreach($pageChunk->chunk(2) as $rowChunk)
                    <tr class="card-row">
                        @foreach($rowChunk as $codeData)
                            <td class="card-cell">
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
                            </td>
                        @endforeach
                        
                        @if($rowChunk->count() == 1)
                            <td class="card-cell"></td>
                        @endif
                    </tr>
                @endforeach
                
                @if(!$loop->last)
                    </table>
                    <div style="page-break-before: always;"></div>
                    <div class="page">
                        <table class="card-table">
                @endif
            @endforeach
        </table>
    </div>
</body>
</html> 