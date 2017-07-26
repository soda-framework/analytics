<?php
    namespace Soda\Analytics\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Analytics\Components\GoogleAPI\GoogleAnalytics;
    use Soda\Analytics\Components\GoogleAPI\GoogleIam;
    use Soda\Analytics\Components\GoogleAPI\GoogleService;
    use Soda\Cms\Http\Controllers\BaseController;

    class ConfigureController extends BaseController
    {

        public function anyIndex() {
            return view('soda-analytics::cms.configure.index');
        }

        public function enableApis() {
            $config = \GoogleConfig::get();

            // enabled services
            $service = new GoogleService();
            $service->EnableAPI('project:' . $config->project_name, config('soda.analytics.apis'));

            // record they have saved
            $config->apis_enabled = true;
            $config->save();

            return redirect(route('soda.analytics'));
        }

        public function createServiceAccount() {
            $iam = new GoogleIam();

            $projectName = \GoogleConfig::get()->project_name;
            $serviceAccount = $iam->CreateServiceAccount($projectName);

            // Save service account
            $config = \GoogleConfig::get();
            $config->service_account_name = $serviceAccount->getName();
            $config->save();

            return redirect(route('soda.analytics'));
        }

        public function createServiceAccountKey(Request $request) {
            $iam = new GoogleIam();

            // Get service account name
            $config = \GoogleConfig::get();
            $serviceAccountName = $request->has('name') ? $request->input('name') : $config->service_account_name;

            $serviceAccountKey = $iam->CreateServiceAccountKey($serviceAccountName);

            // Save service account key
            $config->service_account_credentials_json = $serviceAccountKey;
            $config->save();

            return redirect(route('soda.analytics'));
        }

        public function createServiceAccountAndKey() {
            $iam = new GoogleIam();

            $projectName = \GoogleConfig::get()->project_name;
            $serviceAccount = $iam->CreateServiceAccount($projectName);
            $serviceAccountKey = $iam->CreateServiceAccountKey($serviceAccount->getName());

            // Save service account
            $config = \GoogleConfig::get();
            $config->service_account_name = $serviceAccount->getName();
            $config->service_account_credentials_json = $serviceAccountKey;
            $config->save();

            return redirect(route('soda.analytics'));
        }

        public function addAnalyticsUser() {
            $config = \GoogleConfig::get();

            $service_account_credentials = json_decode($config->service_account_credentials_json);
            $email = $service_account_credentials->client_email;

            $analytics = new GoogleAnalytics();
            $analytics->AddUser($config->account_id, $config->property_id, $config->view_id, $email);

            $config->analytics_user_added = true;
            $config->save();

            return redirect(route('soda.analytics'));
        }


        /**
         * @param Request $request
         *
         * @return \Illuminate\Http\JsonResponse
         *
         * Update the Project Name
         */
        public function postProjectName(Request $request) {
            $validator = Validator::make($request->all(), [
                'project_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            $config = \GoogleConfig::get();
            $config->project_name = $request->input('project_name');
            $config->save();

            return response()->json(['success' => true, 'config' => $config]);
        }

        public function postLoginCredentials(Request $request) {
            $validator = Validator::make($request->all(), [
                'client_id' => 'required',
                'client_secret' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            $config = \GoogleConfig::get();
            $config->client_id = $request->input('client_id');
            $config->client_secret = $request->input('client_secret');
            $config->save();

            return response()->json(['success' => true, 'config' => $config]);
        }

        public function postConfigure(Request $request) {
            $validator = Validator::make($request->all(), [
                'project_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            $config = \GoogleConfig::get();
            $config->project_name = $request->input('project_name');
            $config->save();

            return redirect(route('soda.analytics'));
        }


        // ACCOUNTS/ PROPERTIES
        public function postAccounts() {
            $analytics = new GoogleAnalytics();
            $accounts = $analytics->GetAccounts();
            $accounts = collect($accounts)->sortBy('name')->pluck('name', 'id')->toArray();

            return response()->json(['success' => true, 'accounts' => $accounts]);
        }

        public function postAccountProperties(Request $request) {
            if ( $request->has('account_id') ) {
                $analytics = new GoogleAnalytics();
                $properties = $analytics->GetAccountProperties($request->input('account_id'));
                $properties = collect($properties)->sortBy('name')->pluck('name', 'id')->toArray();

                return response()->json(['success' => true, 'properties' => $properties]);
            }

            return response()->json(['success' => false, 'properties' => []]);
        }

        public function postSaveAccount(Request $request) {
            $validator = Validator::make($request->all(), [
                'account_id'    => 'required',
                'account_name'  => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            $config = \GoogleConfig::get();
            $config->account_id = $request->input('account_id');
            $config->account_name = $request->input('account_name');
            $config->save();

            return response()->json(['success' => true, 'config' => $config]);
        }

        public function postSaveProperty(Request $request) {
            $validator = Validator::make($request->all(), [
                'property_id'   => 'required',
                'property_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            $config = \GoogleConfig::get();
            $config->property_id = $request->input('property_id');
            $config->property_name = $request->input('property_name');
            $config->save();

            $analytics = new GoogleAnalytics();
            $view = $analytics->GetView($config->account_id, $config->property_id);
            $config->view_id = $view->id;
            $config->save();

            return response()->json(['success' => true, 'config' => $config]);
        }

        public function postCreateAccount(Request $request) {
            $validator = Validator::make($request->all(), [
                'account_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            // Create new account in the API
            $analytics = new GoogleAnalytics();
            $account = $analytics->CreateAccount($request->input('account_name'));
            dd($account);

            return response()->json(['success' => true]);
        }

        public function postCreateProperty(Request $request) {
            $validator = Validator::make($request->all(), [
                'account_id'    => 'required',
                'property_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            // Create new property in the API
            $analytics = new GoogleAnalytics();
            $property = $analytics->CreateAccountProperty($request->input('account_id'), $request->input('property_name'));

            return response()->json(['success' => true, 'property_id' => $property->id]);
        }
    }
