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
            'download_code' => ['nullable', 'string', 'max:6'],
        ]);

        // Send email to support email from settings
        try {
            $settings = \App\Models\Settings::first();
            $supportEmail = $settings?->support_email ?? config('mail.from.address');
            
            // Format email body with clear sections
            $body = "SUPPORT REQUEST\n";
            $body .= "================\n\n";
            
            $body .= "FROM:\n";
            $body .= "Name: {$data['name']}\n";
            $body .= "Email: {$data['email']}\n\n";
            
            if (!empty($data['download_code']) && strlen($data['download_code']) === 6) {
                $body .= "DOWNLOAD CODE:\n";
                $body .= $data['download_code'] . "\n\n";
            }
            
            $body .= "MESSAGE:\n";
            $body .= "--------\n";
            $body .= $data['message'] . "\n\n";
            
            $body .= "================\n";
            $body .= "Sent from contact form";

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