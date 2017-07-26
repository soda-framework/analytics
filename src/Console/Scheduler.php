<?php
    namespace Soda\Analytics\Console;

    use DB;
    use Illuminate\Console\Scheduling\Schedule;
    use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
    use Soda\Analytics\Controllers\ScheduleController;
    use Soda\Analytics\Database\Models\Schedule as AnalyticsSchedule;

    class Scheduler extends ConsoleKernel {

        /**
         * The Artisan commands provided by your application.
         *
         * @var array
         */
        protected $commands = [
            'Soda\Analytics\Console\Commands\Email'
        ];

        /**
         * Define the application's command schedule.
         *
         * @param  \Illuminate\Console\Scheduling\Schedule $schedule
         *
         * @return void
         */
        protected function schedule(Schedule $schedule) {
            $analytics_schedules = AnalyticsSchedule::all();

            foreach ($analytics_schedules as $analytics_schedule) {
                // send an email
                $schedule->call(function () use($analytics_schedule) {
                    // send an email
                    ScheduleController::executeSchedule($analytics_schedule);
                });
            }
        }
    }
