<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Tasks\Scheduler;

class Tasks extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Should performance metrics be logged
     * --------------------------------------------------------------------------
     *
     * If true, will log the time it takes for each task to run.
     * Requires the settings table to have been created previously.
     */
    public bool $logPerformance = true;

    /**
     * --------------------------------------------------------------------------
     * Maximum performance logs
     * --------------------------------------------------------------------------
     *
     * The maximum number of logs that should be saved per Task.
     * Lower numbers reduced the amount of database required to
     * store the logs.
     */
    public int $maxLogsPerTask = 10;

    /**
     * Register any tasks within this method for the application.
     * Called by the TaskRunner.
     */
    public function init(Scheduler $schedule)
    {
        $schedule->command('EventCron')->everyMinute(5)->named('event');

        if(getenv('MY_SERVER_NAME') === 'carelabs'){
            $schedule->command('GwCron')->cron('0 11-19 * * *')->named('gw');

            $schedule->command('todayDayOff')->cron('0 9-19 * * *')->named('sendSlackForDayOff');
        }
        
        $schedule->command('Automation')->everyMinute(1)->named('aaCheck');

        $schedule->command('EventDataUpdateCron')->everyMinute(60)->named('eventUpdate');
        $schedule->command('PreparingIssueMessage')->everyMinute(60)->named('preparingIssue');
    }
}
