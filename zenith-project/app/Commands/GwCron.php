<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\HumanResource\HumanResourceController;
use App\Services\LoggerService;

class GwCron extends BaseCommand
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
    protected $name = 'GwCron';

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
        CLI::write('그룹웨어 데이터 연동 작업을 시작합니다.', 'green');
        try{
            $hr = new HumanResourceController();
            $hr->updateUsersByDouzone();
            CLI::write('그룹웨어 데이터 연동 작업 완료', 'green');
        } catch (\Exception $e) {
            $this->showError($e);
        }

        //로그 기록
        $data = [
            'type' => 'tasks',
            'command' => $this->name
        ];

        $logger = new LoggerService();
        $logger->insertLog($data);
    }
}
