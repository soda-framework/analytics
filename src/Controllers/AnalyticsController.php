<?php
    namespace Soda\Analytics\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Soda\Analytics\Components\AnalyticsAccount;
    use Soda\Cms\Http\Controllers\BaseController;

    class AnalyticsController extends BaseController
    {
        public function anyIndex() {
            return view('soda-analytics::cms.index');
        }

        public function postAccounts(){
            $analytics = new AnalyticsAccount();
            $accounts = $analytics->GetAccounts();
            $accounts = collect($accounts)->sortBy('name')->pluck('name', 'id')->toArray();

            return response()->json(['success' => true, 'accounts' => $accounts]);
        }

        public function postAccountProperties(Request $request){
            if( $request->has('account') ) {
                $analytics = new AnalyticsAccount();
                $properties = $analytics->GetAccountProperties($request->input('account'));
                $properties = collect($properties)->sortBy('name')->pluck('name', 'id')->toArray();

                return response()->json(['success' => true, 'properties' => $properties]);
            }

            return response()->json(['success' => false, 'properties' => []]);
        }

        public function postSubmitAccountProperty(Request $request) {
            $validator = Validator::make($request->all(), [
                'account_id'  => 'required',
                'account_name'  => 'required',
                'property_id' => 'required',
                'property_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            $config = \Analytics::config();
            $config->account_id = $request->input('account_id');
            $config->account_name = $request->input('account_name');
            $config->property_id = $request->input('property_id');
            $config->property_name = $request->input('property_name');
            $config->save();

            return redirect(route('soda.analytics'));
        }

        public function postCreateAccount(Request $request) {
            $validator = Validator::make($request->all(), [
                'account_name'  => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            // Create new account in the API
            // TODO: whitelist
            dd($request->input('account_name'));
        }

        public function postCreateProperty(Request $request) {
            $validator = Validator::make($request->all(), [
                'account_id' => 'required',
                'property_name' => 'required',
            ]);
            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'message' => $validator->messages()->first()]);
            }

            // Create new property in the API
            // TODO: whitelist
            $analytics = new AnalyticsAccount();
            $property = $analytics->CreateAccountProperty($request->input('account_id'), $request->input('property_name'));
            dd($property);
        }
    }
