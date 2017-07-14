<?php
    namespace Soda\Analytics\Controllers;

    use Google_Service_Iam_CreateServiceAccountRequest;
    use Google_Service_Iam_ServiceAccount;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Analytics\Components\GoogleAPI\GoogleIam;
    use Soda\Cms\Http\Controllers\BaseController;

    class ConfigureController extends BaseController
    {
        public function anyStep1() {
//            return view('soda-analytics::cms.events');

            $iam = new GoogleIam();
//            $serviceAccount = $iam->CreateServiceAccount('spotify-aami');
//            dd($serviceAccount);
//            $serviceAccountKey = $iam->CreateServiceAccountKey($serviceAccount);


            $serviceAccountKey = $iam->CreateServiceAccountKey();
            dd($serviceAccountKey);

            return view('soda-analytics::cms.configure.service-account-credentials');
        }

        public function anyStep2() {
            return view('soda-analytics::cms.configure.client-credentials');
        }

        public function postConfigure(Request $request) {
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

            return redirect(route('soda.analytics.configure'));
        }
    }
