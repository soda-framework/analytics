<?php
    namespace Soda\Analytics\Controllers;

    use Carbon\Carbon;
    use DateTime;
    use Illuminate\Console\Scheduling\Schedule;
    use Illuminate\Http\Request;
    use Soda\Analytics\Components\GoogleAPI;
    use Soda\Analytics\Components\GoogleAPI\GoogleAnalyticsReporting\GoogleAudience;
    use Soda\Analytics\Components\Helpers;
    use Soda\Analytics\Database\Models\Audience;
    use Soda\Reports\Foundation\AbstractReporter;
    use Zofe\Rapyd\DataFilter\DataFilter;
    use Zofe\Rapyd\DataGrid\DataGrid;

    class AudienceController extends AbstractReporter
    {

        public function query(Request $request) {
            $query = Audience::select('users', 'sessions', 'avg_session_duration');
            return $query;
        }

        public function run(Request $request) {

            dd(app()[Schedule::class]);
            $grid = DataGrid::source($this->query($request));
            $grid->add('users', 'Users');
            $grid->add('sessions', 'Unique Users (Sessions)');
            $grid->add('avg_session_duration', 'Average Session Duration');

            $grid->paginate(20);

            return view('soda-analytics::cms.audience.index', ['report' => $this->report, 'grid' => $grid]);
        }

        public function anyUpdate(Request $request){
            $config = \GoogleConfig::get();
            $config->analytics_from = Carbon::createFromFormat('d/m/Y',$request->input('analytics_from'))->setTime(0,0,0)->toDateTimeString();
            $config->analytics_to = Carbon::createFromFormat('d/m/Y',$request->input('analytics_to'))->setTime(0, 0, 0)->toDateTimeString();
            $config->save();

            // delete old
            Audience::truncate();

            $reporting = new GoogleAudience();
            $reports = $reporting->GetAudience((new Carbon($config->analytics_from))->format('Y-m-d'), (new Carbon($config->analytics_to))->format('Y-m-d'));
            $reports = $reports->getReports();

            foreach ($reports as $report) {
                $report = $reporting->formatResultsArray($report);

                foreach($report as $metrics){
                    $audience = new Audience();
                    $audience->users = $metrics['ga:users'];
                    $audience->sessions = $metrics['ga:sessions'];
                    $audience->avg_session_duration = $reporting->secondsToTime($metrics['ga:avgSessionDuration']);
                    $audience->save();
                }
            }

            // TODO: handle multiple audience reports. Code above can create many, everywhere else is expecting only one (Audience::first())

            return redirect(route('soda.analytics.audience'));
        }

        public function anyExport(Request $request) {
            $grid = DataGrid::source($this->query($request));
            $grid->add('users', 'Users');
            $grid->add('sessions', 'Unique Users (Sessions)');
            $grid->add('avg_session_duration', 'Average Session Duration');

            $as_excel = ['delimiter' => ',', 'enclosure' => '"', 'line_ending' => "\n"];
            $fileResponse = $grid->buildCSV($this->exportFileName(), false, true, $as_excel);

            return $fileResponse;
        }

        public function exportFileName(){
            $config = \GoogleConfig::get();

            return 'Audience_from_' . (new Carbon($config->analytics_from))->format('Y-m-d') . '_to_' . (new Carbon($config->analytics_to))->format('Y-m-d');
        }
    }
