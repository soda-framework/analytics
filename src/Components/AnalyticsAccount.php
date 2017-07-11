<?php
    namespace Soda\Analytics\Components;

    use Google_Client;
    use Google_Service_Analytics;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;
    use Soda\Cms\Http\Controllers\BaseController;

    class AnalyticsAccount extends BaseController
    {
        /**
         * @var $client to be authorized by Google.
         */
        private $client;

        /**
         * @var $analytics Analytics object to be used.
         */
        private $analytics;

        public function __construct() {
            $this->client = $this->AuthenticateCurrentClient();
            $this->analytics = new Google_Service_Analytics($this->client);
        }

        private function AuthenticateCurrentClient() {
            $user = Auth::guard('soda-analytics')->user();
            $token = Session::get('google-token');

            // Authenticate the client.
            $client = new Google_Client();
            $client->setAccessToken($token);

//            // check for expiry
//            if( ($token->created + $token->expires_in) > time() ){
//                $token = $client->fetchAccessTokenWithRefreshToken($user->refresh_token);
//                Session::put('google-token', $token);
//            }

            $client->fetchAccessTokenWithAuthCode($user->code);

            return $client;
        }

        public function GetAccounts($optParams = []) {
            try {
                $accountsObject = $this->analytics->management_accounts->listManagementAccounts($optParams);
                $accounts = $accountsObject->getItems();

                return $accounts;
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }

        public function GetAccountProperties($accountID, $optParams = []) {
            try {
                $propertiesObject = $this->analytics->management_webproperties->listManagementWebproperties($accountID, $optParams);
                $properties = $propertiesObject->getItems();

                return $properties;
            } catch (apiServiceException $e) {
                print 'There was an Analytics API service error '
                    . $e->getCode() . ':' . $e->getMessage();

            } catch (apiException $e) {
                print 'There was a general API error '
                    . $e->getCode() . ':' . $e->getMessage();
            }
        }
    }
