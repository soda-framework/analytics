<?php
    namespace Soda\Analytics\Components;

    use Google_Client;
    use Google_Service_Analytics;
    use Google_Service_Analytics_Webproperty;
    use Google_Service_Iam;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;
    use Soda\Cms\Http\Controllers\BaseController;

    class GoogleAPI extends BaseController
    {
        const CLIENT = 1;
        const ANALYTICS = 2;
        /**
         * @var $client to be authorized by Google.
         */
        protected $client;

        /**
         * @var $analytics Analytics object to be used.
         */
        public $analytics;
        public $iam;

        public function __construct($type=GoogleAPI::CLIENT) {
            if( $type == GoogleAPI::CLIENT ) {
                $this->client = $this->AuthenticateCurrentClient();
            }
            else if ( $type == GoogleAPI::ANALYTICS ) {
                $this->client = $this->AuthenticateAnalyticsClient();
            }
        }

        protected function AuthenticateCurrentClient() {
            $user = Auth::guard('soda-analytics')->user();
            $token = Session::get('google-token');

            // Authenticate the client.
            $client = new Google_Client();
            $client->setAccessToken($token);

            $client->fetchAccessTokenWithAuthCode($user->code);

            return $client;
        }

        protected function AuthenticateAnalyticsClient() {
            $config = \GoogleConfig::get();

            // Authenticate the client.
            $client = new Google_Client();
            $client->setScopes([
                Google_Service_Analytics::ANALYTICS_READONLY,
            ]);
            $client->setAuthConfig((array) json_decode($config->service_account_credentials_json));

            return $client;
        }
    }
