<?php
namespace Soda\Analytics\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Auth;
use Soda\Analytics\Components\Inputs\DropdownVue;
use Soda\Analytics\Components\SodaAnalytics;
use Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Soda\Analytics\Controllers\AuthController;
use Spatie\Analytics\AnalyticsClient;
use Spatie\Analytics\AnalyticsClientFactory;
use Spatie\Analytics\Exceptions\InvalidConfiguration;

//use Spatie\Analytics\Analytics;
//use Spatie\Analytics\AnalyticsClient;

class AnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->loadViewsFrom(__DIR__ . '/../../views', 'soda-analytics');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        $this->app['config']->set('auth.providers.soda-analytics', $this->app->config->get('soda.analytics.auth.provider'));
        $this->app['config']->set('auth.guards.soda-analytics', $this->app->config->get('soda.analytics.auth.guard'));
        $this->app['config']->set('auth.passwords.soda-analytics', $this->app->config->get('soda.analytics.auth.password'));

        app('soda.menu')->menu('sidebar', function ($menu) {
            $menu->addItem('Soda Analytics', [
                'icon'        => 'fa fa-share-alt',
                'label'       => 'Soda Analytics',
                'permissions' => 'access-cms',
            ]);
            $menu['Soda Analytics']->addChild('Authenticate', [
                'url'         => route('soda.analytics.auth'),
                'icon'        => 'fa fa-cog',
                'label'       => 'Authenticate',
                'isCurrent'   => soda_request_is('analytics/auth*'),
                'permissions' => 'access-cms',
            ]);
        });

        Auth::macro('validGoogle', function () {
            return !AuthController::isExpired();
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require_once __DIR__ . '/../../routes/web.php';
            require_once __DIR__ . '/../../routes/api.php';
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        // Publishing configs
        $this->publishes([__DIR__ . '/../../config/' => config_path('soda')], 'soda.analytics');
        $this->publishes([__DIR__ . '/../../public' => public_path('soda/analytics')], 'analytics.assets');

        $analyticsConfig = config('soda.analytics');

        $this->app->bind(AnalyticsClient::class, function () use ($analyticsConfig) {
            return AnalyticsClientFactory::createForConfig($analyticsConfig);
        });

        $this->app->bind(SodaAnalytics::class, function () use ($analyticsConfig) {
            $this->guardAgainstInvalidConfiguration($analyticsConfig);

            $client = app(AnalyticsClient::class);

            return new SodaAnalytics($client, $analyticsConfig['view_id']);
        });

        $this->app->alias(SodaAnalytics::class, 'soda-analytics');

        // register aliases
        AliasLoader::getInstance()->alias('Analytics', 'Soda\Analytics\Components\SodaAnalyticsFacade');

        $this->app['soda.form.registrar']->register('dropdown_vue',DropdownVue::class);
    }

    protected function guardAgainstInvalidConfiguration(array $analyticsConfig = null) {
        if ( empty($analyticsConfig['view_id']) ) {
            throw InvalidConfiguration::viewIdNotSpecified();
        }

        if ( ! file_exists($analyticsConfig['service_account_credentials_json']) ) {
            throw InvalidConfiguration::credentialsJsonDoesNotExist($analyticsConfig['service_account_credentials_json']);
        }
    }
}
