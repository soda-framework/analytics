<?php
    namespace Soda\Analytics\Controllers;

    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Cms\Http\Controllers\BaseController;
    use Spatie\Analytics\Period;

    class AudienceController extends BaseController
    {
        public function anyIndex() {
            $visitors = \Analytics::fetchVisitorsAndPageViews(Period::days(7));

            return view('soda-analytics::cms.audience.index', compact('visitors'));
        }
    }
