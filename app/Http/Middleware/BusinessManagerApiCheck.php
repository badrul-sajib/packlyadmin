<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BusinessManagerApiCheck
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Bearer token present but invalid → Unauthorized
        if ($request->bearerToken() && ! Auth::guard('sanctum')->check()) {
            return failure('Unauthorized', 401);
        }

        // 2. Required custom headers
        $requiredHeaders = ['X-Build-Number', 'X-Platform', 'X-App-Version'];

        foreach ($requiredHeaders as $header) {
            if (! $request->hasHeader($header)) {
                return failure('Please update the application.', 505);
            }
        }

        $buildNumber = $request->header('X-Build-Number');
        $appVersion  = $request->header('X-App-Version');
        $platform    = strtolower($request->header('X-Platform'));

        Log::info('Business Manager API Check Headers', [
            'build_number' => $buildNumber,
            'app_version'  => $appVersion,
            'platform'     => $platform,
        ]);

        // 3. Basic empty checks
        if (
            empty($buildNumber) ||
            empty($appVersion) ||
            (in_array($platform, ['android', 'ios', 'web']) && empty($platform))
        ) {
            return failure('Please update the application.', 505);
        }

        try {
            $settings = \App\Caches\ShopSettingsCache::get();
            // Web maintenance (if merchant has web app)
            // For now, reusing generic web app maintenance or define new if needed.
            // Assuming no separate web app maintenance for merchant for now, or using generic.
            if ($platform === 'web' && isset($settings['business_manager_web_app_maintenance']) && $settings['business_manager_web_app_maintenance'] == 1) {
                return failure('Web App under maintenance.', 503);
            }

            // Android checks
            if ($platform === 'android') {
                if (isset($settings['business_manager_android_app_maintenance']) && $settings['business_manager_android_app_maintenance'] == 1) {
                    return failure('Android App under maintenance.', 503);
                }

                if (isset($settings['business_manager_min_android_version_code']) && $buildNumber < $settings['business_manager_min_android_version_code']) {
                    return failure('Please update the application.', 505);
                }

                if (isset($settings['business_manager_min_android_version']) && version_compare($appVersion, $settings['business_manager_min_android_version'], '<')) {
                    return failure('Please update the application.', 505);
                }
            }

            // iOS checks
            if ($platform === 'ios') {
                if (isset($settings['business_manager_ios_app_maintenance']) && $settings['business_manager_ios_app_maintenance'] == 1) {
                    return failure('iOS App under maintenance.', 503);
                }

                if (isset($settings['business_manager_min_ios_version_code']) && $buildNumber < $settings['business_manager_min_ios_version_code']) {
                    return failure('Please update the application.', 505);
                }

                if (isset($settings['business_manager_min_ios_version']) && version_compare($appVersion, $settings['business_manager_min_ios_version'], '<')) {
                    return failure('Please update the application.', 505);
                }
            }
        } catch (\Exception $e) {
            Log::error('Business Manager API Check Middleware Error: ' . $e->getMessage(), ['exception' => $e]);
        }

        return $next($request);
    }
}
