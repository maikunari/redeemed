<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avery 5371 Business Card Template</title>
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
        
        .card-container {
            width: 7in;
            height: 10in;
            position: relative;
        }
        
        .business-card {
            width: 3.5in;
            height: 2in;
            border: 1px dashed #ddd;
            position: absolute;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        /* Alternating front and back cards */
        .business-card.front {
            background: linear-gradient(135deg, #d4b5b5, #c4a3a3);
            color: white;
        }
        
        .business-card.back {
            background: white;
            color: #333;
            border: 1px solid #f0f0f0;
        }
        
        /* Position each card using absolute positioning */
        .card-1 { top: 0in; left: 0in; }
        .card-2 { top: 0in; left: 3.5in; }
        .card-3 { top: 2in; left: 0in; }
        .card-4 { top: 2in; left: 3.5in; }
        .card-5 { top: 4in; left: 0in; }
        .card-6 { top: 4in; left: 3.5in; }
        .card-7 { top: 6in; left: 0in; }
        .card-8 { top: 6in; left: 3.5in; }
        .card-9 { top: 8in; left: 0in; }
        .card-10 { top: 8in; left: 3.5in; }
        
        /* Front card content */
        .front-content {
            height: 100%;
            display: table;
            width: 100%;
            position: relative;
        }
        
        .front-left {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
            text-align: center;
            padding: 0.3in 0.2in;
        }
        
        .front-right {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
            text-align: center;
            padding: 0.3in 0.2in;
            border-left: 1px solid rgba(255,255,255,0.3);
        }
        
        .qr-code {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 4px;
            margin: 0 auto 8px;
            display: block;
            position: relative;
        }
        
        .website {
            font-size: 11px;
            color: rgba(255,255,255,0.9);
            font-weight: 300;
            letter-spacing: 0.5px;
        }
        
        .name {
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
            font-family: 'Georgia', serif;
        }
        
        .title {
            font-size: 12px;
            color: rgba(255,255,255,0.8);
            font-style: italic;
            font-weight: 300;
            font-family: 'Georgia', serif;
        }
        
        /* Back card content */
        .back-content {
            height: 100%;
            display: table;
            width: 100%;
        }
        
        .back-inner {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 0.3in;
        }
        
        .brand-name-back {
            font-size: 18px;
            font-weight: 600;
            color: #c4a3a3;
            margin-bottom: 12px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-family: 'Georgia', serif;
        }
        
        .download-code {
            font-size: 24px;
            font-weight: bold;
            color: #c4a3a3;
            margin-bottom: 12px;
            letter-spacing: 1.5px;
            font-family: 'Courier New', monospace;
            background: #f8f8f8;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 8px;
        }
        
        .instructions {
            font-size: 10px;
            color: #666;
            margin-bottom: 8px;
            line-height: 1.3;
            font-family: 'Georgia', serif;
        }
        
        .website-back {
            font-size: 11px;
            color: #999;
            margin-bottom: 6px;
            font-weight: 500;
        }
        
        .usage-info {
            font-size: 8px;
            color: #999;
            margin-top: 8px;
        }
        
        /* Remove dashed borders and shadows for clean printing */
        @media print {
            .business-card {
                border: none;
                box-shadow: none;
            }
            
            body {
                background: white;
            }
        }
        
        /* Card numbering for reference */
        .card-number {
            position: absolute;
            top: 2px;
            left: 2px;
            font-size: 8px;
            color: rgba(255,255,255,0.5);
            font-weight: normal;
            z-index: 10;
        }
        
        .back .card-number {
            color: #ccc;
        }
        
        @media print {
            .card-number {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="card-container">
            @foreach($codes->chunk(10) as $pageChunk)
                @foreach($pageChunk as $index => $codeData)
                    @php
                        $cardNumber = $index + 1;
                        $isOdd = ($cardNumber % 2 == 1);
                    @endphp
                    
                    @if($isOdd)
                        <!-- Card {{ $cardNumber }} - Front -->
                        <div class="business-card card-{{ $cardNumber }} front">
                            <div class="card-number">{{ $cardNumber }}</div>
                            <div class="front-content">
                                <div class="front-left">
                                    <img src="data:image/svg+xml;base64,{{ $codeData['qr_code'] }}" 
                                         alt="QR Code for {{ $codeData['code'] }}" 
                                         class="qr-code">
                                    <div class="website">{{ parse_url($website_url ?? config('app.url'), PHP_URL_HOST) }}</div>
                                </div>
                                <div class="front-right">
                                    <div class="name">{{ $brand_name ?? 'Redeemed' }}</div>
                                    <div class="title">{{ $codeData['file_title'] ?? 'Digital Content' }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Card {{ $cardNumber }} - Back -->
                        <div class="business-card card-{{ $cardNumber }} back">
                            <div class="card-number">{{ $cardNumber }}</div>
                            <div class="back-content">
                                <div class="back-inner">
                                    <div class="brand-name-back">{{ $brand_name ?? 'Redeemed' }}</div>
                                    <div class="download-code">{{ $codeData['code'] }}</div>
                                    <div class="instructions">{{ $card_instructions ?? 'Visit the website below and enter your download code to access your digital content.' }}</div>
                                    <div class="website-back">{{ parse_url($website_url ?? config('app.url'), PHP_URL_HOST) }}/redeem</div>
                                    <div class="usage-info">{{ $codeData['usage_info'] }}@if($codeData['expires_at']) • Expires: {{ $codeData['expires_at'] }}@endif</div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
                
                @if(!$loop->last)
                    </div>
                    <div style="page-break-before: always;"></div>
                    <div class="page">
                        <div class="card-container">
                @endif
            @endforeach
        </div>
    </div>
</body>
</html> 