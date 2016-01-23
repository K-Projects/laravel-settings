<?php
namespace IonutMilica\LaravelSettings;

use Closure;
use Illuminate\Contracts\Routing\TerminableMiddleware;

class SavableMiddleware implements TerminableMiddleware
{
    /**
     * @var SettingsContract
     */
    protected $settings;

    /**
     * @param SettingsContract $settings
     */
    public function __construct(SettingsContract $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Perform any final actions for the request lifecycle.
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    public function terminate($request, $response)
    {
        $this->settings->save();
    }

}
