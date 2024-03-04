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
        if($this->db->tableExists($this->tableName)){
            $this->db->table($this->tableName)->insert($data);
        }else{
            $createResult = $this->makeZenithLogTable();
            if($createResult){
                $this->db->table($this->tableName)->insert($data);
            }
        }
    }

    public function makeZenithLogTable()
    {
        $sql = "CREATE TABLE `$this->tableName` (
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
        $result = $this->db->query($sql);
        return $result;
    }
}
?>