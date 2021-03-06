<?php namespace Arcanedev\Localization\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class     LocaleCookieRedirect
 *
 * @package  Arcanedev\Localization\Middleware
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class LocaleCookieRedirect extends Middleware
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure                  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // If the request URL is ignored from localization.
        if ($this->shouldIgnore($request)) return $next($request);

        $segment = $request->segment(1, null);

        if ($this->localization->isLocaleSupported($segment))
            return $next($request)->withCookie(cookie()->forever('locale', $segment));

        $locale  = $request->cookie('locale', null);

        if ($locale !== null && ! $this->isDefaultLocaleHidden($locale)) {
            if ( ! is_null($redirect = $this->getLocalizedRedirect($locale)))
                return $redirect->withCookie(cookie()->forever('locale', $segment));
        }

        return $next($request);
    }
}
