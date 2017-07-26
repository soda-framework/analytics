<?php
    namespace Soda\Analytics\Console\Commands;

    use Illuminate\Console\Command;
    use Illuminate\Http\Request;
    use Soda\Analytics\Controllers\ScheduleController;
    use Soda\Analytics\Database\Models\Schedule;

    class Email extends Command {
        protected $signature = 'soda-analytics:schedules';
        protected $description = 'Run all of Soda Analytics schedules.';

        /**
         * Execute the console command.
         *
         * @return mixed
         */
        public function handle(Request $request) {
            $analytics_schedules = Schedule::all();

            foreach ($analytics_schedules as $analytics_schedule) {
                // send an email
                $scheduler = new ScheduleController();
                $scheduler->executeSchedule($request,$analytics_schedule);
            }

            $this->info('All of Soda Analytics schedules were run successfully!');
        }
    }
