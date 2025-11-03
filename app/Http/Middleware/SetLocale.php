<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Available locales for the application.
     */
    protected array $availableLocales = ['en', 'ru', 'uk', 'es', 'fr', 'de', 'ko', 'zh-TW', 'zh-CN'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('locale')) {
            // User has manually selected a locale, use that
            app()->setLocale(session('locale'));
        } else {
            // Auto-detect locale from browser's Accept-Language header
            $detectedLocale = $this->detectLocale($request);

            if ($detectedLocale) {
                app()->setLocale($detectedLocale);
            }
            // Otherwise, falls back to config default ('en')
        }

        return $next($request);
    }

    /**
     * Detect the user's preferred locale from browser settings.
     */
    protected function detectLocale(Request $request): ?string
    {
        $preferredLanguages = $request->getLanguages();

        foreach ($preferredLanguages as $language) {
            // Direct match (e.g., 'en', 'ru', 'uk')
            if (in_array($language, $this->availableLocales)) {
                return $language;
            }

            // Match language part (e.g., 'en-US' -> 'en', 'zh-Hans' -> 'zh')
            $languageCode = substr($language, 0, 2);
            if (in_array($languageCode, $this->availableLocales)) {
                return $languageCode;
            }

            // Special handling for Chinese variants
            if (str_starts_with($language, 'zh')) {
                // Traditional Chinese: zh-TW, zh-HK, zh-Hant
                if (in_array($language, ['zh-TW', 'zh-HK']) || str_contains($language, 'Hant')) {
                    return 'zh-TW';
                }
                // Simplified Chinese: zh-CN, zh-Hans
                if (in_array($language, ['zh-CN', 'zh-SG']) || str_contains($language, 'Hans')) {
                    return 'zh-CN';
                }
            }
        }

        return null; // No match found, will use config default
    }
}
