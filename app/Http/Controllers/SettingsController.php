<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        return Settings::first() ?? new Settings(['site_name' => '']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // 2MB Max
            'support_email' => ['nullable','email','max:255'],
            'contact_subtitle' => ['nullable','string','max:255'],
            'contact_thankyou' => ['nullable','string','max:255'],
            'card_website_url' => ['nullable', 'url', 'max:255'],
            'card_brand_name' => ['nullable', 'string', 'max:255'],
            'card_instructions' => ['nullable', 'string'],
            'card_qr_instruction' => ['nullable', 'string', 'max:100'],
        ]);

        $settings = Settings::first() ?? new Settings();
        $settings->site_name = $request->site_name;
        $settings->support_email = $request->support_email;
        $settings->contact_subtitle = $request->contact_subtitle;
        $settings->contact_thankyou = $request->contact_thankyou;
        $settings->card_website_url = $request->card_website_url;
        $settings->card_brand_name = $request->card_brand_name;
        $settings->card_instructions = $request->card_instructions;
        $settings->card_qr_instruction = $request->input('card_qr_instruction', '');
        $settings->save();

        if ($request->hasFile('logo')) {
            // Remove old logo if it exists
            if ($settings->getFirstMedia('logos')) {
                $settings->getFirstMedia('logos')->delete();
            }

            // Add new logo
            $settings->addMedia($request->file('logo'))
                    ->toMediaCollection('logos');
        }

        return back()->with('success', 'Settings updated successfully.');
    }
} 