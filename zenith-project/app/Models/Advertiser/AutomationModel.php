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

    public function getAutomations()
    {
        $builder = $this->zenith->table('admanager_automation aa');
        $builder->select('aa.seq, aas.idx as aas_idx, aas.exec_type, aas.type_value, DATE_FORMAT(aas.exec_time, "%H:%i") as exec_time, DATE_FORMAT(aas.ignore_start_time, "%H:%i") as ignore_start_time, DATE_FORMAT(aas.ignore_end_time, "%H:%i") as ignore_end_time, aas.exec_week, aas.month_type, aas.month_day, aas.month_week, aas.reg_datetime as aas_reg_datetime, aar.seq as aar_seq, aar.exec_timestamp');
        $builder->join('aa_schedule aas', 'aas.idx = aa.seq', 'left');
        $builder->join('aa_target aat', 'aat.idx = aa.seq', 'left');
        $builder->join('aa_conditions aac', 'aac.idx = aa.seq', 'left');
        $builder->join('aa_executions aae', 'aae.idx = aa.seq', 'left');
        $builder->join('aa_result aar', 'aar.idx = aa.seq', 'left');

        $result = $builder->get()->getResultArray();
        return $result;
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
        //$this->zenith->transStart();
        $builder = $this->zenith->table('aa_schedule');
        $result = $builder->insert($data);
        //$result = $this->zenith->transComplete();
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
