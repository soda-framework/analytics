<?php
    namespace Soda\Analytics\Controllers;

    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Analytics\Components\GoogleAPI\GoogleAnalyticsReporting\GoogleEvents;
    use Soda\Analytics\Components\Helpers;
    use Soda\Analytics\Database\Models\Event;
    use Soda\Reports\Foundation\AbstractReporter;
    use Zofe\Rapyd\DataFilter\DataFilter;
    use Zofe\Rapyd\DataGrid\DataGrid;

    class EventsController extends AbstractReporter
    {

        public function query(Request $request) {
            $query = Event::select('category', 'action', 'label', 'value', 'total', 'unique');
            return $query;
        }

        public function run(Request $request) {
            $filter = DataFilter::source($this->query($request));
            $filter->add('category', 'Category', 'text');
            $filter->add('action', 'Action', 'text');
            $filter->add('label', 'Label', 'text');
            $filter->submit('search');
            $filter->reset('reset');

            $grid = DataGrid::source($filter);
            $grid->add('category', 'Category',true)->cell(function ($value, $row) use ($request) {
                return '<a href="'.Helpers::merge_get_url($request->fullUrl(), ['category='.$value, 'search=1'],'page').'">'. $value.'</a>';
            });
            $grid->add('action', 'Action', true)->cell(function ($value, $row) use ($request) {
                return '<a href="' . Helpers::merge_get_url($request->fullUrl(), ['action=' . $value, 'search=1'], 'page') . '">' . $value . '</a>';
            });
            $grid->add('label', 'Label', true)->cell(function ($value, $row) use ($request) {
                return '<a href="' . Helpers::merge_get_url($request->fullUrl(), ['label=' . $value, 'search=1'], 'page') . '">' . $value . '</a>';
            });
            $grid->add('value', 'Value', true);
            $grid->add('total', 'Total Events', true);
            $grid->add('unique', 'Unique Events', true);

            $grid->paginate(20);//->getGrid($this->getGridView());

            return view('soda-analytics::cms.events.index', ['report' => $this->report, 'grid' => $grid, 'filter'=> $filter]);
        }

        public function anyIndexAlt() {
            $reporting = new GoogleEvents();
            $reports = $reporting->GetEvents('7daysAgo', 'today');
            $reports = $reports->getReports();

            return view('soda-analytics::cms.events.index', compact('reports'));
        }

        public function anyUpdate(Request $request) {
            AnalyticsController::updateDates($request);
            $config = \GoogleConfig::get();

            $this->update($config->analytics_from, $config->analytics_to);

            return redirect(route('soda.analytics.events'));
        }

        public function update($from, $to = null) {
            $from = (new Carbon($from))->format('Y-m-d');
            $to = isset($to) ? (new Carbon($to))->format('Y-m-d') : Carbon::now()->format('Y-m-d');

            // delete old
            Event::truncate();

            $reporting = new GoogleEvents();
            $reports = $reporting->GetEvents($from, $to);
            $reports = $reports->getReports();

            foreach ($reports as $report) {
                $report = $reporting->formatResultsArray($report);

                foreach ($report as $category => $categoryValue) {
                    foreach ($categoryValue as $action => $actionValue) {
                        foreach ($actionValue as $label => $labelValue) {

                            $event = new Event();
                            $event->category = substr($category, 0, 100);
                            $event->action = substr($action, 0, 100);
                            $event->label = substr($label, 0, 100);
                            $event->value = substr($labelValue['ga:eventValue'], 0, 100);
                            $event->total = substr($labelValue['ga:totalEvents'], 0, 100);
                            $event->unique = substr($labelValue['ga:uniqueEvents'], 0, 100);
                            $event->save();

                        }
                    }
                }
            }
        }

        public function anyExport(Request $request, $file_name = null) {
            $file_name = $file_name ? $file_name : $this->exportFileName();

            $filter = DataFilter::source($this->query($request));
            $filter->add('category', 'Category', 'text');
            $filter->add('action', 'Action', 'text');
            $filter->add('label', 'Label', 'text');
            $filter->submit('search');
            $filter->reset('reset');

            $filter->action = 'search'; // fix for scheduler

            $grid = DataGrid::source($filter);
            $grid->add('category', 'Category');
            $grid->add('action', 'Action');
            $grid->add('label', 'Label');
            $grid->add('value', 'Value');
            $grid->add('total', 'Total Events');
            $grid->add('unique', 'Unique Events');

            $filter->build();
            $as_excel = ['delimiter' => ',', 'enclosure' => '"', 'line_ending' => "\n"];

            $fileResponse = $grid->buildCSV($file_name, false, true, $as_excel);

            return $fileResponse;
        }

        public function exportFileName() {
            $config = \GoogleConfig::get();

            return 'Events_from_' . (new Carbon($config->analytics_from))->format('Y-m-d') . '_to_' . (new Carbon($config->analytics_to))->format('Y-m-d');
        }
    }
