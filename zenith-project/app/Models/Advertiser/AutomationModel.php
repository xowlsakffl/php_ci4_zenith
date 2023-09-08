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
        $this->zenith->transStart();
        $builder = $this->zenith->table('admanager_automation');
        $builder->insert($data);
        //$insertId = $this->zenith->insertID();
        $result = $this->zenith->transComplete();
        return $result;
    }

    public function createAutomationSchedule($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('aa_schedule');
        $builder->insert($data);
        $result = $this->zenith->transComplete();
        return $result;
    }

    public function createAutomationTarget($data)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('aa_target');
        $builder->insert($data);
        $result = $this->zenith->transComplete();
        return $result;
    }

    public function createAutomationCondition($args)
    {
        $this->zenith->transStart();
        foreach ($args['data'] as $d) {
            $data = [
                'idx' => $args['idx'],
                'order' => $d['order'],
                'type' => $d['type'],
                'type_value' => $d['type_value'],
                'compare' => $d['compare'],
                'operation' => $d['operation'],
            ];

            $builder = $this->zenith->table('aa_conditions');
            $result = $builder->insert($data);

            if($result == false){
                return $result;
            }
        }
        $result = $this->zenith->transComplete();
        
        return $result;
    }

    public function createAutomationExecution($args)
    {
        $this->zenith->transStart();
        foreach ($args['data'] as $d) {
            $data = [
                'idx' => $args['idx'],
                'order' => $d['order'],
                'media' => $d['media'],
                'type' => $d['type'],
                'id' => $d['id'],
                'exec_type' => $d['exec_type'],
                'exec_value' => $d['exec_value'],
            ];

            $builder = $this->zenith->table('aa_executions');
            $result = $builder->insert($data);
            if($result == false){
                return $result;
            }
        }
        $result = $this->zenith->transComplete();

        return $result;
    }

    public function updateAutomation($data, $seq)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('admanager_automation');
        $builder->where('seq', $seq);
        $builder->update($data);
        //$insertId = $this->zenith->insertID();
        $result = $this->zenith->transComplete();
        return $result;
    }

    public function updateAutomationSchedule($data, $idx)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('aa_schedule');
        $builder->where('idx', $idx);
        $builder->update($data);
        $result = $this->zenith->transComplete();
        return $result;
    }

    public function updateAutomationTarget($data, $idx)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('aa_target');
        $builder->where('idx', $idx);
        $builder->update($data);
        $result = $this->zenith->transComplete();
        return $result;
    }

    public function updateAutomationCondition($args, $idx)
    {
        $this->zenith->transStart();

        $builder = $this->zenith->table('aa_conditions');
        $builder->where('idx', $idx);
        $builder->delete();

        foreach ($args['data'] as $d) {
            $data = [
                'idx' => $idx,
                'order' => $d['order'],
                'type' => $d['type'],
                'type_value' => $d['type_value'],
                'compare' => $d['compare'],
                'operation' => $d['operation'],
            ];
            $builder = $this->zenith->table('aa_conditions');
            $builder->insert($data);
        }

        $result = $this->zenith->transComplete();
        return $result;
    }

    public function updateAutomationExecution($args, $idx)
    {
        $this->zenith->transStart();

        $builder = $this->zenith->table('aa_executions');
        $builder->where('idx', $idx);
        $builder->delete();

        foreach ($args['data'] as $d) {
            $data = [
                'idx' => $idx,
                'order' => $d['order'],
                'media' => $d['media'],
                'type' => $d['type'],
                'id' => $d['id'],
                'exec_type' => $d['exec_type'],
                'exec_value' => $d['exec_value'],
            ];
            $builder = $this->zenith->table('aa_executions');
            $builder->insert($data);
        }

        $result = $this->zenith->transComplete();
        return $result;
    }

    public function deleteAutomation($seq)
    {
        $this->zenith->transStart();
        $builder = $this->zenith->table('admanager_automation');
        $builder->where('seq', $seq);
        $builder->delete();
        $result = $this->zenith->transComplete();
        return $result;
    }
}
