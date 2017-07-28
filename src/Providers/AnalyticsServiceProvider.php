<?php
    namespace Soda\Analytics\Providers;

    use Illuminate\Events\Dispatcher;
    use Illuminate\Foundation\AliasLoader;
    use Illuminate\Support\Facades\Auth;
    use Soda\Analytics\Components\Inputs\DropdownVue;
    use Route;
    use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
    use Soda\Analytics\Console\Commands\Email;
    use Soda\Analytics\Console\Scheduler;
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

            if ( $this->app->runningInConsole() ) {
                $this->commands([
                    Email::class,
                ]);
            }

            app('soda.menu')->menu('sidebar', function ($menu) {
                $menu->addItem('Analytics', [
                    'icon'        => 'fa fa-share-alt',
                    'label'       => 'Analytics',
                    'permissions' => 'access-cms',
                ]);
                $menu['Analytics']->addChild('Configure', [
                    'url'         => route('soda.analytics.configure'),
                    'icon'        => 'fa fa-cog',
                    'label'       => 'Configure',
                    'isCurrent'   => soda_request_is('analytics/configure*'),
                    'permissions' => 'access-cms',
                ]);
                $menu['Analytics']->addChild('Audience', [
                    'url'         => route('soda.analytics.audience'),
                    'icon'        => 'fa fa-users',
                    'label'       => 'Audience',
                    'isCurrent'   => soda_request_is('analytics/audience*'),
                    'permissions' => 'access-cms',
                ]);
                $menu['Analytics']->addChild('Events', [
                    'url'         => route('soda.analytics.events'),
                    'icon'        => 'fa fa-tasks',
                    'label'       => 'Events',
                    'isCurrent'   => soda_request_is('analytics/events*'),
                    'permissions' => 'access-cms',
                ]);
                $menu['Analytics']->addChild('Scheduler', [
                    'url'         => route('soda.analytics.scheduler'),
                    'icon'        => 'fa fa-clock-o',
                    'label'       => 'Schedules',
                    'isCurrent'   => soda_request_is('analytics/scheduler*'),
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
