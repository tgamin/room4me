<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Locale
{
    const LOCALES = ['en', 'fr'];

    public function handle(Request $request, Closure $next)
    {
        $session = session();

        if (!$session->has('locale')) {
            $session->put('locale', $request->getPreferredLanguage(self::LOCALES));
        }

        $lang = $request->get('lang');

        if (in_array($lang, self::LOCALES)) {
            $session->put('locale', $lang);
        }

        app()->setLocale($session->get('locale'));

        return $next($request);
    }
}
