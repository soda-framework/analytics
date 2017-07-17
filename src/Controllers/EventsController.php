<?php
    namespace Soda\Analytics\Controllers;

    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Cms\Http\Controllers\BaseController;
    use Spatie\Analytics\Period;

    class EventsController extends BaseController
    {
        public function anyIndex() {
            $events = \Analytics::fetchEvents(Period::days(7));

            return view('soda-analytics::cms.events.index', compact('events'));
        }
    }
