<?php

namespace App\Services;

class LoggerService
{
    protected $db, $currentDate, $tableName;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->currentDate = date('Ym');
        $this->tableName = 'zenith_logs_'.$this->currentDate;
    }
    
    public function insertLog($data)
    {
        $this->db->table($this->tableName)->insert($data);
    }
}
?>