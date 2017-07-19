<?php
    namespace Soda\Analytics\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Schema;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Analytics\Components\GoogleAPI\GoogleAnalyticsReporting;
    use Soda\Analytics\Components\Helpers;
    use Soda\Analytics\Database\Models\Event;
    use Soda\Cms\Http\Controllers\BaseController;
    use Soda\Reports\Foundation\AbstractReporter;
    use Spatie\Analytics\Period;
    use Zofe\Rapyd\DataGrid\DataGrid;

    class EventsController extends AbstractReporter
    {

        public function query(Request $request) {
            $query = Event::select('category', 'action', 'label', 'value', 'total', 'unique');

            foreach($request->all() as $param=>$value){
                if( Schema::hasColumn((new Event)->getTable(), $param) ){
                    $query = $query->where($param, $value);
                }
            }

            return $query;
        }

        public function run(Request $request) {
            $grid = DataGrid::source($this->query($request));
            $grid->add('category', 'Category')->cell(function ($value, $row) use ($request) {
                return '<a href="'.Helpers::merge_get_url($request->fullUrl(),'category='.$value,'page').'">'. $value.'</a>';
            });
            $grid->add('action', 'Action')->cell(function ($value, $row) use ($request) {
                return '<a href="' . Helpers::merge_get_url($request->fullUrl(), 'action=' . $value, 'page') . '">' . $value . '</a>';
            });
            $grid->add('label', 'Label')->cell(function ($value, $row) use ($request) {
                return '<a href="' . Helpers::merge_get_url($request->fullUrl(), 'label=' . $value, 'page') . '">' . $value . '</a>';
            });
            $grid->add('value', 'Value');
            $grid->add('total', 'Total');
            $grid->add('unique', 'Unique');

            $grid->paginate(20)->getGrid($this->getGridView());

            return view('soda-analytics::cms.events.index', ['report' => $this->report, 'grid' => $grid]);
        }

        public function anyIndexAlt() {
            $reporting = new GoogleAnalyticsReporting();
            $reports = $reporting->GetEvents('7daysAgo', 'today');
            $reports = $reports->getReports();

            return view('soda-analytics::cms.events.index', compact('reports'));
        }

        public function anyUpdate(){
            $reporting = new GoogleAnalyticsReporting();
            $reports = $reporting->GetEvents('7daysAgo', 'today');
            $reports = $reports->getReports();

            foreach ($reports as $report) {
                $report = $reporting->formatResultsArray($report);

                foreach($report as $category => $categoryValue){
                    foreach ($categoryValue as $action => $actionValue) {
                        foreach ($actionValue as $label => $labelValue) {

                            $event = new Event();
                            $event->category = $category;
                            $event->action = $action;
                            $event->label = $label;
                            $event->value = $labelValue['Value'];
                            $event->total = $labelValue['Total Events'];
                            $event->unique = $labelValue['Unique Events'];
                            $event->save();

                        }
                    }
                }
            }

            return redirect(route('soda.analytics.events'));
        }
    }
