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
    }
