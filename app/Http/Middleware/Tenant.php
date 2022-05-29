<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Exception\TenantException;
use App\Modules\Tenant\Providers\TenantProvider;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Tenant
{
    /**
     * Create a new middleware instance.
     *
     * @param TenantProvider $tenantProvider
     * @param string $tenantHost
     */
    public function __construct(private string $tenantHost, private TenantProvider $tenantProvider)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|JsonResponse|RedirectResponse
     * @throws TenantException
     */
    public function handle(Request $request, Closure $next): Response|JsonResponse|RedirectResponse
    {
        $match = null;
        $haveMatch = preg_match('/' . $this->tenantHostRegex() . '/', $request->getHost(), $match);

        $failure = function (TenantException $exception) use ($request) : RedirectResponse {
            if ($request->isXmlHttpRequest()) {
                throw $exception;
            }

            return new RedirectResponse(config('app.url'));
        };

        // didn't match any URL we know about OR didn't match any tenant name
        if (!$haveMatch || 2 !== count($match)) {
            return $failure(TenantException::createInvalidUrl());
        }

        $tenant = $this->tenantProvider->getByActiveSlug($match[1]);
        if (!$tenant) {
            return $failure(TenantException::createInvalidTenant($match[1]));
        }

        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }

    /**
     * Get a regular expression matching the tenant URL and all of its subdomains.
     *
     * @return string
     */
    private function tenantHostRegex(): string
    {
        return '^(.+)\.' . preg_quote($this->tenantHost) . '$';
    }
}
