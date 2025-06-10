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

        // Send email to support email from settings
        try {
            $settings = \App\Models\Settings::first();
            $supportEmail = $settings?->support_email ?? config('mail.from.address');
            
            $body = $data['message'];
            if (!empty($data['download_code'])) {
                $body .= "\n\nDownload code: " . $data['download_code'];
            }
            $body .= "\n\nFrom: {$data['name']} <{$data['email']}>";

            Mail::raw($body, function ($msg) use ($supportEmail, $data) {
                $msg->to($supportEmail)
                    ->from(config('mail.from.address'), $data['name'] . ' (via ' . config('mail.from.name') . ')')
                    ->replyTo($data['email'], $data['name'])
                    ->subject('Support Request from ' . $data['name']);
            });
        } catch (\Throwable $e) {
            Log::error('Support mail failed', ['error' => $e->getMessage()]);
        }

        return back();
    }
} 