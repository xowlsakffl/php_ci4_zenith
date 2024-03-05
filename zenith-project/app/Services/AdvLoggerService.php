<?php

namespace App\Services;

class AdvLoggerService
{
    protected $db, $currentDate, $tableName;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }
    
    public function insertLog($data)
    {
        $this->db->table('adv_change_logs')->insert($data);
    }
}
?>