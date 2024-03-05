<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use DateTime;

class ZenithLogTable extends BaseCommand
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
    protected $name = 'ZenithLogTable';

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
    protected $usage = 'command:name [arguments] [options]';

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
        if (date('m', strtotime('+1 day')) == date('m')) {
            return;
        }

        $db = \Config\Database::connect();
        $nextMonth = date('Ym', strtotime('+1 month'));
        $tableName = 'zenith_logs_'.$nextMonth;
        $sql = "CREATE TABLE `$tableName` (
            `type` VARCHAR(100),
            `scheme` VARCHAR(100),
            `host` VARCHAR(255),
            `path` VARCHAR(255),
            `method` VARCHAR(100),
            `command` VARCHAR(255),
            `query_string` TEXT,
            `data` TEXT,
            `content_type` VARCHAR(100),
            `remote_addr` VARCHAR(100),
            `server_addr` VARCHAR(100),
            `nickname` VARCHAR(100),
            `datetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )";
        $db->query($sql);
    }
}
