<?php
    namespace Soda\Analytics\Controllers;

    use Google_Client;
    use Google_Service_Analytics;
    use Google_Service_Iam;
    use Google_Service_Oauth2;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;
    use Soda\Analytics\Database\Models\User;
    use Soda\Cms\Http\Controllers\BaseController;

    class AuthController extends BaseController
    {
        public static function googleClient() {
            // Create the client object and set the authorization configuration from JSON file.
            $client = new Google_Client();
            $client->setClientId(\GoogleConfig::get()->client_id);
            $client->setClientSecret(\GoogleConfig::get()->client_secret);
            $client->setRedirectUri(route('soda.analytics.auth.callback'));
            $client->addScope(Google_Service_Analytics::ANALYTICS_EDIT);

            $client->addScope(Google_Service_Iam::CLOUD_PLATFORM);

            $client->addScope("email");
            $client->addScope("profile");
            $client->setAccessType("online");

            return $client;
        }

        public static function isExpired(){
            $googleToken = session('google-token');
            if( ($googleToken['created'] + $googleToken['expires_in']) > time() ){
                return false;
            }
            return true;
        }

        /**
         * Redirect the user to the Google authentication
         */
        public function redirectToProvider() {
            $client = self::googleClient();

            $auth_url = $client->createAuthUrl();

            return redirect($auth_url);
        }

        /**
         * Obtain the user information from Google.
         *
         * @return redirect to the app.
         */
        public function handleProviderCallback(Request $request) {
            // Handle authorization flow from the server.
            if ( ! $request->has('code') ) {
                return redirect('soda.analytics.auth');
            } else {

                // Authenticate the client, and get required informations.
                $client = self::googleClient();
                $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

                // Store the tokens in the session.
                Session::put('google-token', $token);

                $service = new Google_Service_Oauth2($client);
                $userInfo = $service->userinfo->get();

                $user = User::where('google_id', $userInfo->id)->first();
                if ( ! $user ) { // If no match, new user.
                    $user = new User();
                }

                $user->name = $userInfo->name;
                $user->google_id = $userInfo->id;
                $user->email = $userInfo->email;
                $user->refresh_token = $client->getRefreshToken();
                $user->code = $request->get('code');
                $user->save();

                Auth::guard('soda-analytics')->login($user);

                return redirect(route('soda.analytics'));
            }

        }
    }
