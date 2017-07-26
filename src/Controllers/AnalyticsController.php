<?php
    namespace Soda\Analytics\Controllers;

    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Analytics\Components\GoogleAPI\GoogleAnalytics;
    use Soda\Analytics\Components\GoogleAPI\GoogleIam;
    use Soda\Cms\Http\Controllers\BaseController;

    class AnalyticsController extends BaseController
    {
        public function anyIndex() {
            return view('soda-analytics::cms.index');
        }

        public function postConfig() {
            $config = \GoogleConfig::get();
            return response()->json(['config' => $config]);
        }

        public static function updateDates(Request $request, $model=null){
            $model = $model ? $model : \GoogleConfig::get();
            if ( $request->has('analytics_from') ) $model->analytics_from = Carbon::createFromFormat('d/m/Y', $request->input('analytics_from'))->setTime(0, 0, 0)->toDateTimeString();
            $model->analytics_to = $request->has('analytics_to') ? Carbon::createFromFormat('d/m/Y', $request->input('analytics_to'))->setTime(0, 0, 0)->toDateTimeString() : null;
            $model->save();
        }
    }
