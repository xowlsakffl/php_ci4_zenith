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

        $schedule->command('GwCron')->everyMinute(30)->betweenHours(10,19)->named('gw');
        
        $exec_updateUsersByDouzone = 'php '.FCPATH.'index.php hr/updateUsersByDouzone';
        $schedule->shell($exec_updateUsersByDouzone)->hours([10,12,14,16,18])->minutes([0])->named('updateUsersByDouzone');

        $schedule->command('todayDayOff')->cron('0 * * * *')->named('sendSlackForDayOff');
    }
}
