<?php
namespace App\ThirdParty\jira_api;

use CodeIgniter\Database\RawSql;
class JIRADB
{
    private $db, $db2, $zenith;
    private $sltDB;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect('jira');
        $this->zenith = \Config\Database::connect();
    }

    public function updateToken($data)
    {
        $insertData = [
            'uid' => 0,
            'access_token' => $data['access_token'] ?? '',
            'refresh_token' => $data['refresh_token'] ?? '',
            'expires_time' => date('Y-m-d H:i:s', time() + $data['expires_in']),
        ];
        $this->db->transStart();
        $builder = $this->db->table('api_info');
        $builder->setData($insertData);
        $updateTime = ['update_time' => new RawSql('NOW()')];
        $builder->updateFields($updateTime, true);
        $builder->upsert();
        $result = $this->db->transComplete();

        return $result;
    }

    public function getToken()
    {
        $builder = $this->db->table('api_info');
        $builder->select('access_token, refresh_token, expires_time');
        $builder->where('uid', 0);
        $result = $builder->get()->getRowArray();

        return $result;
    }
}
