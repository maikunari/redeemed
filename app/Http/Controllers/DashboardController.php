<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'settings' => Settings::first() ?? new Settings(),
            'stats' => [
                'file_count' => \App\Models\File::count(),
                'download_count' => \App\Models\DownloadCode::sum('usage_count'),
                'codes_total' => \App\Models\DownloadCode::count(),
                'codes_redeemed' => \App\Models\DownloadCode::where('usage_count', '>', 0)->count(),
            ]
        ]);
    }
} 