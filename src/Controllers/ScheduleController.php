<?php
    namespace Soda\Analytics\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Validator;
    use Mockery\CountValidator\Exception;
    use Soda\Analytics\Database\Models\Schedule;
    use Soda\Reports\Foundation\AbstractReporter;
    use Zofe\Rapyd\DataGrid\DataGrid;

    class ScheduleController extends AbstractReporter
    {
        const EVENTS = 'Events';
        const AUDIENCE = 'Audience';

        public function query(Request $request) {
            $query = Schedule::select('id','type');

            return $query;
        }

        public function run(Request $request) {
            $grid = DataGrid::source($this->query($request));
            $grid->add('type', 'Type');
            $grid->add('id', 'Edit')->cell(function ($value, $row) use ($request) {
                return '<a class="btn btn-primary" href="' . route('soda.analytics.scheduler.update', $value) . '">Edit</a>';
            });
            $grid->add('id', 'Delete')->cell(function ($value, $row) use ($request) {
                return '<a class="btn btn-primary" href="' . route('soda.analytics.scheduler.delete', $value) . '">Delete</a>';
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
            $schedule = new Schedule();
            return view('soda-analytics::cms.scheduler.update',compact('schedule'));
        }

        public function getUpdate(Request $request, $id) {
            $schedule = Schedule::find($id);

            return view('soda-analytics::cms.scheduler.update', compact('schedule'));
        }

        public function postUpdate(Request $request, $id=null){
            $schedule = $id ? Schedule::find($id) : new Schedule();

            $validator = Validator::make($request->all(), [
                'type' => 'required|max:100',
                'emails' => 'required|max:255',
            ]);

            if( $validator->passes() ) {
                $schedule->type = $request->input('type');
                $schedule->emails = json_encode($request->input('emails'));
                $schedule->save();

                return redirect(route('soda.analytics.scheduler.update.get', $schedule->id));
            }
            return redirect()->back()->withErrors($validator);
        }

        public function anyDelete(Request $request, $id) {
            Schedule::destroy($id);

            return redirect(route('soda.analytics.scheduler'));
        }

        public static function executeSchedule($schedule){
            if( $schedule->type == ScheduleController::AUDIENCE ) {
                $exporter = new AudienceController();
            }
            else if ( $schedule->type == ScheduleController::EVENTS ) {
                $exporter = new EventsController();
            }
            else{
                throw new Exception();
            }

            $request = new Request();
            $filePath = 'analytics/' . $exporter->exportFileName() . '.csv';
            $storagePath = Storage::disk('soda-local')->getDriver()->getAdapter()->getPathPrefix();

            $csv = $exporter->anyExport($request);
            Storage::disk('soda-local')->put($filePath, $csv->getContent());

            Mail::send('soda-analytics::emails.schedule', [], function ($message) use ($schedule, $storagePath, $filePath, $exporter) {
                $message->from('hello@madeinkatana.com', 'MadeInKatana');
				foreach(json_decode($schedule->emails) as $email){
                    $message->to($email);
                }
				$message->attach($storagePath . '/' . $filePath);
                $message->subject($exporter->exportFileName() . ' ' . date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
            });

            Storage::disk('soda-local')->delete($filePath);
        }
    }
