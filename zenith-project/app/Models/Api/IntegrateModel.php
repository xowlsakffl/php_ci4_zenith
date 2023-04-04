<?php

namespace App\Models\Api;

use CodeIgniter\Model;

class IntegrateModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'event_leads';
    protected $primaryKey       = 'seq';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    //protected $allowedFields    = ['', ''];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function __construct()
    {
        
    }

    public function getEventLead()
    {
        $builder = $this->select("CONCAT('evt_', info.seq) AS seq, adv.NAME AS advertiser, med.media, adv.is_stop, info.description AS tab_name, a.*");
        $builder->from('event_information as info');
        $builder->join('event_advertiser as adv', "info.advertiser = adv.seq AND adv.is_stop = 0", 'left');
        $builder->join('event_media as med', 'info.media = med.seq', 'left');
        $builder->join('event_leads as a', 'a.event_seq = info.seq', 'left'); 
        /* $builder->where('DATE(a.reg_date) >=', $data['sdate']);
        $builder->where('DATE(a.reg_date) <=', $data['edate']); */
        $builder->where('a.is_deleted', 0);
        $builder->orderBy('a.seq', 'DESC');
        $results = $builder->get();
        return $results;
    }
}
