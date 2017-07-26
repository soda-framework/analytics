<?php
    namespace Soda\Analytics\Controllers;

    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Validator;
    use Mockery\CountValidator\Exception;
    use Soda\Analytics\Database\Models\Schedule;
    use Soda\Reports\Foundation\AbstractReporter;
    use Symfony\Component\HttpFoundation\ParameterBag;
    use Zofe\Rapyd\DataGrid\DataGrid;

    class ScheduleController extends AbstractReporter
    {
        const EVENTS = 'Events';
        const AUDIENCE = 'Audience';
        const EVENTS_AND_AUDIENCE = 'Events & Audience';

        public function query(Request $request) {
            $query = Schedule::select('id','type','emails','request');

            return $query;
        }

        public function run(Request $request) {
            $grid = DataGrid::source($this->query($request));
            $grid->add('type', 'Type');
            $grid->add('emails', 'Emails')->cell(function ($value, $row) use ($request) {
                $string = '';
                if ( $value ) {
                    foreach (json_decode($value, true) as $key => $email) {
                        $string .= $email . '<br/>';
                    }
                }

                return $string;
            });
            $grid->add('request', 'Filters')->cell(function ($value, $row) use ($request) {
                $string = '';
                if( $value ){
                    foreach(json_decode($value, true) as $key=>$filter){
                        $string .= ucwords($key) .': '. $filter.'<br/>';
                    }
                }
                return $string;
            });
            $grid->add('id', 'Action')->cell(function ($value, $row) use ($request) {
                return '<a class="btn btn-primary" href="' . route('soda.analytics.scheduler.update.get', $value) . '">Edit</a>' .
                       ' <a class="btn btn-warning" href="' . route('soda.analytics.scheduler.run', $value) . '">Run</a>' .
                       ' <a class="btn btn-danger" href="' . route('soda.analytics.scheduler.delete', $value) . '">Delete</a>';
            });
            $grid->paginate(20);

            return view('soda-analytics::cms.scheduler.index', ['report' => $this->report, 'grid' => $grid]);
        }

        public function postConfigUpdate(Request $request) {
            $config = \GoogleConfig::get();
            $config->schedule_frequency = $request->input('schedule_frequency');
            $config->save();

            return redirect()->back();
        }

        public function anyCreate(Request $request) {
            $config = \GoogleConfig::get();

            $schedule = new Schedule();
            $schedule->analytics_from = $config->analytics_from;

            return view('soda-analytics::cms.scheduler.update',compact('schedule'));
        }

        public function anyCreateFromEvent(Request $request) {
            $config = \GoogleConfig::get();

            $schedule = new Schedule();
            $schedule->analytics_from = $config->analytics_from;
            $schedule->type = ScheduleController::EVENTS;
            $schedule->request = json_encode($request->all());
            return view('soda-analytics::cms.scheduler.update',compact('schedule'));
        }

        public function getUpdate(Request $request, $id) {
            $schedule = Schedule::find($id);

            return view('soda-analytics::cms.scheduler.update', compact('schedule'));
        }

        public function postUpdate(Request $request, $id=null){
            $schedule = $id ? Schedule::find($id) : new Schedule();

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'type' => 'required|max:100',
                'emails' => 'required|max:255',
                'analytics_from' => 'required|max:255',
            ]);

            if( $validator->passes() ) {
                $schedule->name = $request->input('name');
                $schedule->type = $request->input('type');
                $schedule->emails = json_encode($request->input('emails'));
                $schedule->request = $request->input('request');
                $schedule->save();
                AnalyticsController::updateDates($request, $schedule);

                return redirect(route('soda.analytics.scheduler.update.get', $schedule->id));
            }
            return redirect()->back()->withErrors($validator);
        }

        public function anyDelete(Request $request, $id) {
            Schedule::destroy($id);

            return redirect(route('soda.analytics.scheduler'));
        }

        public function runSchedule(Request $request, $id){
            $schedule = Schedule::find($id);

            $this->executeSchedule($request, $schedule);

            return view('soda-analytics::cms.scheduler.update', compact('schedule'));
        }

        public function executeSchedule(Request $request, $schedule){
            $files = [];
            if( $schedule->type == ScheduleController::AUDIENCE || $schedule->type == ScheduleController::EVENTS_AND_AUDIENCE ) {
                $files[] = $this->exportFile($request, $schedule, new AudienceController());
            }
            if ( $schedule->type == ScheduleController::EVENTS || $schedule->type == ScheduleController::EVENTS_AND_AUDIENCE ) {
                $files[] = $this->exportFile($request, $schedule, new EventsController());
            }
            if( count($files) <= 0 ) {
                throw new Exception('Schedule type not set');
            }

            Mail::send('soda-analytics::emails.schedule', [], function ($message) use ($schedule, $files) {
                $message->from('hello@madeinkatana.com', 'MadeInKatana');
				foreach(json_decode($schedule->emails) as $email){
                    $message->to($email);
                }
                foreach ($files as $file) {
                    $message->attach($file->full_path);
                }
                $message->subject($schedule->name . ' - ' . $schedule->type . ' - ' . date('Y-m-d', $_SERVER['REQUEST_TIME']));
            });

            foreach ($files as $file) {
                Storage::disk('soda-local')->delete($file->path);
            }
        }

        public function exportFile(Request $request, $schedule, $exporter){
            // update new data
            $exporter->update($schedule->analytics_from, $schedule->analytics_to);

            // apply filters for export
            if ( $schedule->request ) $request->merge(json_decode($schedule->request, true));

            $file_name = $this->scheduleFileName($schedule);
            $filePath = 'analytics/' . $file_name . '.csv';
            $storagePath = Storage::disk('soda-local')->getDriver()->getAdapter()->getPathPrefix();
            $csv = $exporter->anyExport($request, $file_name);

            Storage::disk('soda-local')->put($filePath, $csv->getContent());

            $file = new \stdClass();
            $file->path = $filePath;
            $file->full_path = $storagePath . '/' . $filePath;
            $file->file_name = $file_name;

            return $file;
        }

        public function scheduleFileName($schedule) {
            $name = strtoupper(str_slug($schedule->type)) . '_from_' . (new Carbon($schedule->analytics_from))->format('Y-m-d');
            if( $schedule->analytics_to ) $name .= '_to_' . (new Carbon($schedule->analytics_to))->format('Y-m-d');
            $name .= '_' . uniqid();

            return $name;
        }
    }
