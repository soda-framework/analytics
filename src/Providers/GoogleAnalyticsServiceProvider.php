<?php
namespace Soda\Analytics\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Soda\Analytics\Components\Inputs\DropdownVue;
use Soda\Analytics\Components\SodaAnalytics;
use Route;
use Soda\Analytics\Controllers\AuthController;
use Soda\Analytics\Database\Models\Config;
use Spatie\Analytics\AnalyticsClient;
use Spatie\Analytics\AnalyticsClientFactory;
use Spatie\Analytics\Exceptions\InvalidConfiguration;

//use Spatie\Analytics\Analytics;
//use Spatie\Analytics\AnalyticsClient;

class GoogleAnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $analyticsConfig = config('soda.analytics');

        $this->app->bind(AnalyticsClient::class, function () use ($analyticsConfig) {
            $analyticsConfig['service_account_credentials_json'] = json_decode(\GoogleConfig::get()->service_account_credentials_json, true);
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
    }

    protected function guardAgainstInvalidConfiguration(array $analyticsConfig = null) {
        if ( empty($analyticsConfig['view_id']) ) {
            throw InvalidConfiguration::viewIdNotSpecified();
        }

//        if ( ! file_exists($analyticsConfig['service_account_credentials_json']) ) {
//            throw InvalidConfiguration::credentialsJsonDoesNotExist($analyticsConfig['service_account_credentials_json']);
//        }
    }

    public function provides()
    {
        return [
            'soda-analytics',
            AnalyticsClient::class,
            SodaAnalytics::class
        ];
    }
}
