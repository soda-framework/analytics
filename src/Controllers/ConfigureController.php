<?php
    namespace Soda\Analytics\Controllers;

    use Exception;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Analytics\Components\GoogleAPI\GoogleAnalytics;
    use Soda\Analytics\Components\GoogleAPI\GoogleIam;
    use Soda\Analytics\Components\GoogleAPI\GoogleService;
    use Soda\Cms\Http\Controllers\BaseController;

    class ConfigureController extends BaseController {

        public function anyIndex() {
            return view('soda-analytics::cms.configure.index');
        }

        public function enableApis() {
            $config = \GoogleConfig::get();

            // enabled services
            $service = new GoogleService();
            $service->EnableAPI('project:' . $config->project_id, config('soda.analytics.apis'));

            // record they have saved
            $config->apis_enabled = true;
            $config->save();

            return redirect(route('soda.analytics.configure'));
        }

        public function createServiceAccountAndKey() {
            $iam = new GoogleIam();

            $projectID = \GoogleConfig::get()->project_id;
            $serviceAccount = $iam->CreateServiceAccount($projectID);
            $serviceAccountKey = $iam->CreateServiceAccountKey($serviceAccount->getName());

            // Save service account
            $config = \GoogleConfig::get();
            $config->service_account_credentials_json = $serviceAccountKey;
            $config->save();

            return redirect(route('soda.analytics.configure'));
        }

        public function addAnalyticsUser() {
            $config = \GoogleConfig::get();

            $service_account_credentials = json_decode($config->service_account_credentials_json);
            $email = $service_account_credentials->client_email;

            $analytics = new GoogleAnalytics();
            $analytics->AddUser($config->account_id, $config->property_id, $config->view_id, $email);

            $config->analytics_user_added = true;
            $config->save();

            return redirect(route('soda.analytics.configure'));
        }


        /**
         * @param Request $request
         *
         * @return \Illuminate\Http\JsonResponse
         *
         * Update the Project ID
         */
        public function postProjectID(Request $request) {
            $validator = Validator::make($request->all(), [
                'project_id' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            try{
                $config = \GoogleConfig::get();
                $config->project_id = $request->input('project_id');
                $config->save();

                return response()->json(['success' => true, 'config' => $config]);
            } catch (Exception $ex) {
                return response()->json(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        public function postLoginCredentials(Request $request) {
            $validator = Validator::make($request->all(), [
                'client_id'     => 'required',
                'client_secret' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            try{
                $config = \GoogleConfig::get();
                $config->client_id = $request->input('client_id');
                $config->client_secret = $request->input('client_secret');
                $config->save();

                return response()->json(['success' => true, 'config' => $config]);
            } catch (Exception $ex) {
                return response()->json(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        // ACCOUNTS/ PROPERTIES
        public function postAccounts() {
            try {
                $analytics = new GoogleAnalytics();
                $accounts = $analytics->GetAccounts();
                $accounts = collect($accounts)->sortBy('name')->pluck('name', 'id')->toArray();

                return response()->json(['success' => true, 'accounts' => $accounts]);
            } catch(Exception $ex){
                return response()->json(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        public function postAccountProperties(Request $request) {
            $validator = Validator::make($request->all(), [
                'account_id'   => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            $analytics = new GoogleAnalytics();
            $properties = $analytics->GetAccountProperties($request->input('account_id'));
            $properties = collect($properties)->sortBy('name')->pluck('name', 'id')->toArray();

            return response()->json(['success' => true, 'properties' => $properties]);
        }

        public function postAccountPropertyViews(Request $request) {
            $validator = Validator::make($request->all(), [
                'account_id' => 'required',
                'property_id' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            try {
                $analytics = new GoogleAnalytics();
                $views = $analytics->GetAccountPropertyViews($request->input('account_id'), $request->input('property_id'));
                $views = collect($views)->sortBy('name')->pluck('name', 'id')->toArray();

                return response()->json(['success' => true, 'views' => $views]);
            } catch (Exception $ex) {
                return response()->json(['success' => false, 'message' => $ex->getMessage()]);
            }

        }

        public function postSaveAccount(Request $request) {
            $validator = Validator::make($request->all(), [
                'account_id'   => 'required',
                'account_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            try{
                $config = \GoogleConfig::get();
                $config->account_id = $request->input('account_id');
                $config->account_name = $request->input('account_name');
                $config->save();

                return response()->json(['success' => true, 'config' => $config]);
            } catch (Exception $ex) {
                return response()->json(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        public function postSaveProperty(Request $request) {
            $validator = Validator::make($request->all(), [
                'property_id'   => 'required',
                'property_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            try{
                $config = \GoogleConfig::get();
                $config->property_id = $request->input('property_id');
                $config->property_name = $request->input('property_name');
                $config->save();

                return response()->json(['success' => true, 'config' => $config]);
            } catch (Exception $ex) {
                return response()->json(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        public function postSaveView(Request $request) {
            $validator = Validator::make($request->all(), [
                'view_id'   => 'required',
                'view_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            try{
                $config = \GoogleConfig::get();
                $config->view_id = $request->input('view_id');
                $config->view_name = $request->input('view_name');
                $config->save();

                return response()->json(['success' => true, 'config' => $config]);
            } catch (Exception $ex) {
                return response()->json(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        public function postCreateAccount(Request $request) {
            $validator = Validator::make($request->all(), [
                'account_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            try{
                // Create new account in the API
                $analytics = new GoogleAnalytics();
                $account = $analytics->CreateAccount($request->input('account_name'));

                return response()->json(['success' => true, 'account_id' => $account->id, 'account_name' => $account->name]);
            } catch (Exception $ex) {
                return response()->json(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        public function postCreateProperty(Request $request) {
            $validator = Validator::make($request->all(), [
                'account_id'    => 'required',
                'property_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            try{
                // Create new property in the API
                $analytics = new GoogleAnalytics();
                $property = $analytics->CreateAccountProperty($request->input('account_id'), $request->input('property_name'));

                return response()->json(['success' => true, 'property_id' => $property->id, 'property_name' => $property->name]);
            } catch (Exception $ex) {
                return response()->json(['success' => false, 'message' => $ex->getMessage()]);
            }
        }

        public function postCreateView(Request $request) {
            $validator = Validator::make($request->all(), [
                'account_id'    => 'required',
                'property_id' => 'required',
                'view_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            try{
                // Create new property in the API
                $analytics = new GoogleAnalytics();
                $view = $analytics->CreateAccountPropertyView($request->input('account_id'), $request->input('property_id'), $request->input('view_name'));

                return response()->json(['success' => true, 'view_id' => $view->id, 'view_name' => $view->name]);
            } catch (Exception $ex) {
                return response()->json(['success' => false, 'message' => $ex->getMessage()]);
            }
        }
    }
