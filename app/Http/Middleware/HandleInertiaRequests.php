<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Settings;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'version' => !$request->routeIs('codes.show-form') ? config('version.version') : null,
            'settings' => $request->routeIs('codes.show-form') ? [
                'site_name' => app(Settings::class)->first()?->site_name,
                'logo' => app(Settings::class)->first()?->logo_url,
            ] : null,
        ]);
    }
}
