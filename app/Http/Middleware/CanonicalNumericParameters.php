<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanonicalNumericParameters
{
    /**
     * Приводит числовые параметры URL к каноничной форме: убирает ведущие нули
     * у целых и хвостовые — у дробных. Если в запросе была неканоничная форма,
     * отвечает 301 на каноничный URL — чтобы /anime/01 и /ranobe/1/100.50/7.0
     * не создавали дублей в глазах поисковиков.
     */
    public function handle(Request $request, Closure $next, string ...$names): Response
    {
        $route = $request->route();

        if ($route === null || $route->getName() === null) {
            return $next($request);
        }

        $parameters = $route->parameters();
        $changed = false;

        foreach ($names as $name) {
            if (! array_key_exists($name, $parameters)) {
                continue;
            }

            $raw = (string) $parameters[$name];

            if (! preg_match('/^\d+(\.\d+)?$/', $raw)) {
                continue;
            }

            $canonical = str_contains($raw, '.')
                ? (string) (float) $raw
                : (string) (int) $raw;

            if ($canonical !== $raw) {
                $parameters[$name] = $canonical;
                $changed = true;
            }
        }

        if (! $changed) {
            return $next($request);
        }

        $url = route($route->getName(), $parameters);

        if ($request->getQueryString() !== null) {
            $url .= '?'.$request->getQueryString();
        }

        return redirect()->to($url, 301);
    }
}
