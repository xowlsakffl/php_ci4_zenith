<?php

namespace App\Commands;

use App\Controllers\Api\JiraController;
use App\Services\LoggerService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class PreparingIssueMessageCron extends BaseCommand
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
    protected $name = 'PreparingIssueMessage';

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
    protected $usage = 'command:PreparingIssueMessage [arguments] [options]';

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
        $hour = date('G');
        $week = date('w');
        if($hour < "10" || $hour >= "19" || $week == "0" || $week == "6") return;
        $jira = new JiraController;
        $jira->getIssuePreparing();

        //로그 기록
        $data = [
            'type' => 'tasks',
            'command' => $this->name
        ];

        $logger = new LoggerService();
        $logger->insertLog($data);
    }
}
