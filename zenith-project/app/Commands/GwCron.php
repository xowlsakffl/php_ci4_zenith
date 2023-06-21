<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

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
            $zenith = \Config\Database::connect();
            $gwdb = \Config\Database::connect('gwdb');
            $not_members = [2315, 2418, 2416, 2419, 2508, 2123, 2127, 2411];
    
            $sql = "SELECT * FROM users u LEFT JOIN auth_groups_users agu ON u.id = agu.user_id WHERE agu.group = 'user'";
            $zenithUser = $zenith->query($sql)->getResultArray();
            if(!empty($zenithUser)){
                $sql = "SELECT emp_seq, email_addr, SUBSTRING_INDEX(emp_name, '_', -1) AS emp_name, path_name FROM v_user_info WHERE work_status <> '001' AND (path_name LIKE '디지털마케팅 사업본부%') ORDER BY CAST(emp_seq AS int)";
                $gwUsers = $gwdb->query($sql)->getResultArray();
                foreach ($gwUsers as $gwUser) {
                    if (!in_array($gwUser['emp_seq'], $not_members)) {
                        $sql = "UPDATE users u LEFT JOIN auth_groups_users agu ON u.id = agu.user_id AND agu.group = 'user' LEFT JOIN auth_identities ai ON u.id = ai.user_id SET emp_seq = {$gwUser['emp_seq']}, user_belong = '{$gwUser['path_name']}' WHERE ai.secret = '{$gwUser['email_addr']}@carelabs.co.kr' and agu.group = 'user'";
                        $zenith->query($sql);
                    }
                }
                CLI::write('그룹웨어 데이터 연동 작업 완료', 'green');
            }
        } catch (\Exception $e) {
            $this->showError($e);
        }
    }
}
