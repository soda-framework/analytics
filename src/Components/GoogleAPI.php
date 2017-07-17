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
        /**
         * @var $client to be authorized by Google.
         */
        protected $client;

        /**
         * @var $analytics Analytics object to be used.
         */
        public $analytics;
        public $iam;

        public function __construct() {
            $this->client = $this->AuthenticateCurrentClient();
            $this->analytics = new Google_Service_Analytics($this->client);
            $this->iam = new Google_Service_Iam($this->client);
        }

        private function AuthenticateCurrentClient() {
            $user = Auth::guard('soda-analytics')->user();
            $token = Session::get('google-token');

            // Authenticate the client.
            $client = new Google_Client();
            $client->setAccessToken($token);

            $client->fetchAccessTokenWithAuthCode($user->code);

            return $client;
        }
    }
