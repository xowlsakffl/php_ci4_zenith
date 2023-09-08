<?php

namespace App\Models\Advertiser;

use CodeIgniter\Model;

class AutomationModel extends Model
{
    protected $zenith;
    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
    }

    public function createAutomation($data)
    {
        $builder = $this->zenith->table('admanager_automation');
        $result = $builder->insert($data);
        //$insertId = $this->zenith->insertID();
        return $result;
    }

    public function createAutomationTarget($data)
    {
        $builder = $this->zenith->table('aa_target');
        $result = $builder->insert($data);

        return $result;
    }

    public function createAutomationCondition($data)
    {
        $builder = $this->zenith->table('aa_conditions');
        $result = $builder->insert($data);

        return $result;
    }

    public function createAutomationExecution($data)
    {
        $builder = $this->zenith->table('aa_executions');
        $result = $builder->insert($data);

        return $result;
    }

    public function updateAutomation($data, $seq)
    {
        $builder = $this->zenith->table('admanager_automation');
        $builder->where('seq', $seq);
        $result = $builder->update($data);
        //$insertId = $this->zenith->insertID();
        return $result;
    }

    public function updateAutomationTarget($data, $idx)
    {
        $builder = $this->zenith->table('aa_target');
        $builder->where('idx', $idx);
        $result = $builder->update($data);

        return $result;
    }

    public function updateAutomationCondition($data, $idx)
    {
        $builder = $this->zenith->table('aa_conditions');
        $builder->where('idx', $idx);
        $result = $builder->update($data);

        return $result;
    }

    public function updateAutomationExecution($data, $idx)
    {
        $builder = $this->zenith->table('aa_executions');
        $builder->where('idx', $idx);
        $result = $builder->update($data);

        return $result;
    }

    public function deleteAutomation($seq)
    {
        $builder = $this->zenith->table('admanager_automation');
        $builder->where('seq', $seq);
        $result = $builder->delete();

        return $result;
    }
}
