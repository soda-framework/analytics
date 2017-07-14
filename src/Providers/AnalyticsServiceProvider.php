<?php
    namespace Soda\Analytics\Providers;

    use Illuminate\Foundation\AliasLoader;
    use Illuminate\Support\Facades\Auth;
    use Soda\Analytics\Components\Inputs\DropdownVue;
    use Route;
    use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
    use Soda\Analytics\Controllers\AuthController;
    use Soda\Analytics\Database\Models\Config;

//use Spatie\Analytics\Analytics;
//use Spatie\Analytics\AnalyticsClient;

    class AnalyticsServiceProvider extends ServiceProvider {

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
        public function boot() {
            parent::boot();

            $this->loadViewsFrom(__DIR__ . '/../../views', 'soda-analytics');
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

            $this->app['config']->set('auth.providers.soda-analytics', $this->app->config->get('soda.analytics.auth.provider'));
            $this->app['config']->set('auth.guards.soda-analytics', $this->app->config->get('soda.analytics.auth.guard'));
            $this->app['config']->set('auth.passwords.soda-analytics', $this->app->config->get('soda.analytics.auth.password'));

            app('soda.menu')->menu('sidebar', function ($menu) {
                $menu->addItem('Soda Analytics', [
                    'icon'        => 'fa fa-share-alt',
                    'label'       => 'Soda Analytics',
                    'permissions' => 'access-cms',
                ]);
                $menu['Soda Analytics']->addChild('Step 1: Configure', [
                    'url'         => route('soda.analytics.configure.step-1'),
                    'icon'        => 'fa fa-cog',
                    'label'       => 'Step 1: Configure',
                    'isCurrent'   => soda_request_is('analytics/configure*'),
                    'permissions' => 'access-cms',
                ]);
                $menu['Soda Analytics']->addChild('Step 2: Configure', [
                    'url'         => route('soda.analytics.configure.step-2'),
                    'icon'        => 'fa fa-cog',
                    'label'       => 'Step 2: Configure',
                    'isCurrent'   => soda_request_is('analytics/configure*'),
                    'permissions' => 'access-cms',
                ]);
                $menu['Soda Analytics']->addChild('Step 3: Account & Property', [
                    'url'         => Auth::guard('soda-analytics')->check() && Auth::guard('soda-analytics')->validGoogle() ? route('soda.analytics') : route('soda.analytics.auth'),
                    'icon'        => 'fa fa-cog',
                    'label'       => 'Step 2: Account & Property',
                    'isCurrent'   => soda_request_is('analytics/auth*'),
                    'permissions' => 'access-cms',
                ]);
            });

            Auth::macro('validGoogle', function () {
                return ! AuthController::isExpired();
            });
        }

        /**
         * Define the routes for the application.
         *
         * @return void
         */
        public function map() {
            Route::group([
                'middleware' => 'web',
                'namespace'  => $this->namespace,
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
        public function register() {
            // Publishing configs
            $this->publishes([__DIR__ . '/../../config/' => config_path('soda')], 'soda.analytics');
            $this->publishes([__DIR__ . '/../../public' => public_path('soda/analytics')], 'analytics.assets');

            $this->app->register(GoogleAnalyticsServiceProvider::class);

            $this->app->singleton('soda-google-config', function () {
                return Config::firstOrNew([]);
            });

            AliasLoader::getInstance()->alias('GoogleConfig', 'Soda\Analytics\Components\SodaGoogleConfigFacade');

            $this->app['soda.form.registrar']->register('dropdown_vue', DropdownVue::class);
        }
    }
