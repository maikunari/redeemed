<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'message' => ['required', 'string'],
            'download_code' => ['nullable','regex:/^[2-9]{6}$/'],
        ]);

        // Example: send email to site admin; replace as needed.
        try {
            $body = $data['message'];
            if (!empty($data['download_code'])) {
                $body .= "\n\nDownload code: " . $data['download_code'];
            }
            $body .= "\n\nFrom: {$data['name']} <{$data['email']}>";

            Mail::raw($body, function ($msg) {
                $msg->to(config('mail.from.address'))
                    ->subject('Support Request');
            });
        } catch (\Throwable $e) {
            Log::error('Support mail failed', ['error' => $e->getMessage()]);
        }

        return back();
    }
} 