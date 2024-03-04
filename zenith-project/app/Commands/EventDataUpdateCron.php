<?php

namespace App\Commands;

use App\Controllers\Api\JiraController;
use App\Services\LoggerService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class EventDataUpdateCron extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'CodeIgniter';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'EventDataUpdateCron';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = '';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:EventDataUpdateCron [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $jira = new JiraController;
        $jira->getIssueEventData();

        //로그 기록
        $data = [
            'type' => 'tasks',
            'command' => $this->name
        ];

        $logger = new LoggerService();
        $logger->insertLog($data);
    }
}
