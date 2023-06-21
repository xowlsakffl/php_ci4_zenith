<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class EventCron extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'cron';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'EventCron';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = '';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('DB수 업데이트 작업을 시작합니다.', 'green');
        try {
            $zenith = \Config\Database::connect();
            $sql = "SELECT event_seq, site, count(*) AS db_count, DATE(reg_date) AS date FROM event_leads WHERE status = 1 GROUP BY event_seq, site, DATE(reg_date)";
            $result = $zenith->query($sql)->getResultArray();
            $data = [];

            foreach($result as $row) {
                $data[] = "('{$row['event_seq']}', '{$row['site']}', '{$row['date']}', '{$row['db_count']}', NOW())";
            }

            $sql = "INSERT INTO event_leads_count(seq, site, date, db_count, update_time) VALUES ".implode(',', $data)." ON DUPLICATE KEY 
                        UPDATE db_count = VALUES(db_count), update_time = VALUES(update_time)";
            $result = $zenith->query($sql);
            CLI::write('DB수 업데이트 작업 완료', 'green');
        } catch (\Exception $e) {
            $this->showError($e);
        }
    }
}
