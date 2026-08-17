<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Guest-facing admin routes (login, signup, password reset, OTP) use a
     * bare "guest" root — no dashboard theme/layout assets — kept separate
     * from the "admin" dashboard root used by the rest of /admin/*.
     */
    private const GUEST_ROUTES = [
        'admin/login',
        'admin/signup',
        'admin/forgot-password',
        'admin/verify-otp',
        'admin/reset-password',
    ];

    /**
     * Resolve the root template for the current request.
     *
     * Requests under /admin render using the AdminLTE-based "admin" layout
     * instead of the public site's "app" layout, except for the guest auth
     * routes above, which use the bare "guest" layout.
     */
    public function rootView(Request $request): string
    {
        if ($request->is(...self::GUEST_ROUTES)) {
            return 'guest';
        }

        return $request->is('admin', 'admin/*') ? 'admin' : $this->rootView;
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => fn () => $request->user() ? [
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'joined' => $request->user()->created_at?->format('M Y'),
                    'photo_url' => $request->user()->photo ? '/storage/'.$request->user()->photo : null,
                ] : null,
            ],
        ];
    }
}
