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
        $builder->select('
        aa.seq as aa_seq, 
        aas.idx as aas_idx, 
        aas.exec_type as aas_exec_type, 
        aas.type_value as aas_type_value, 
        DATE_FORMAT(aas.exec_time, "%H:%i") as aas_exec_time, 
        DATE_FORMAT(aas.ignore_start_time, "%H:%i") as aas_ignore_start_time, 
        DATE_FORMAT(aas.ignore_end_time, "%H:%i") as aas_ignore_end_time, 
        aas.exec_week as aas_exec_week, 
        aas.month_type as aas_month_type, 
        aas.month_day as aas_month_day, 
        aas.month_week as aas_month_week, 
        aas.reg_datetime as aas_reg_datetime, 
        aat.idx as aat_idx,
        aat.type as aat_type,
        aat.media as aat_media,
        aat.id as aat_id,
        aar.seq as aar_seq, 
        aar.exec_timestamp as aar_exec_timestamp
        ');
        $builder->join('aa_schedule aas', 'aas.idx = aa.seq', 'left');
        $builder->join('aa_target aat', 'aat.idx = aa.seq', 'left');
        $builder->join('aa_conditions aac', 'aac.idx = aa.seq', 'left');
        $builder->join('aa_executions aae', 'aae.idx = aa.seq', 'left');
        $builder->join('aa_result aar', 'aar.idx = aa.seq', 'left');
        $builder->groupBy('aa.seq');
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getTargetCompany($data)
    {
        $companyBuilder = $this->zenith->table('companies c');
        $companyBuilder->select('c.id, ca.media, GROUP_CONCAT(DISTINCT ca.ad_account_id) as ad_account_id');
        $companyBuilder->join('company_adaccounts ca', 'c.id = ca.company_id');
        $companyBuilder->where('c.id', $data['aat_id']);
        $companyBuilder->groupBy('ca.media');
        $companies = $companyBuilder->get()->getResultArray();
        $builders = [];
        foreach ($companies as $company) {
            switch ($company['media']) {
                case 'facebook':
                    $facebookBuilder = $this->getFacebookByCompany($data, $company);
                    $builders[] = $facebookBuilder;
                    break;
                case 'google':
                    $googleBuilder = $this->getGoogleByCompany($data, $company);
                    $builders[] = $googleBuilder;
                    break;
                case 'kakao':
                    $kakaoBuilder = $this->getKakaoByCompany($data, $company);
                    $builders[] = $kakaoBuilder;
                    break;
                default:
                    break;
            }
        }
        $unionBuilder = null;
        foreach ($builders as $builder) {
            if ($unionBuilder) {
                $unionBuilder->union($builder);
                
            } else {
                $unionBuilder = $builder;
            }
        }
        $builder = $this->zenith->newQuery()->fromSubquery($unionBuilder, 'adv');
        $result = $builder->get()->getResultArray();
        return $result;
    }

    private function getFacebookByCompany($data, $company)
    {
        $facebookBuilder = $this->zenith->table('z_facebook.fb_ad_insight_history A');
        $facebookBuilder->select(' 
            SUM(D.budget) AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.spend) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.impressions) AS impressions,
            SUM(A.inline_link_clicks) AS click,
        ');
        $facebookBuilder->join('z_facebook.fb_ad B', 'A.ad_id = B.ad_id');
        $facebookBuilder->join('z_facebook.fb_adset C', 'B.adset_id = C.adset_id');
        $facebookBuilder->join('z_facebook.fb_campaign D', 'C.campaign_id = D.campaign_id');
        $facebookBuilder->join('z_facebook.fb_ad_account E', 'D.account_id = E.ad_account_id');
        $facebookBuilder->join('zenith.company_adaccounts F', 'E.ad_account_id = F.ad_account_id AND F.media = "facebook"');
        $facebookBuilder->join('zenith.companies G', 'F.company_id = G.id');
        $facebookBuilder->where('DATE(A.date) >=', date('Y-m-d'));
        $facebookBuilder->where('DATE(A.date) <=', date('Y-m-d'));
        $facebookBuilder->whereIn('E.ad_account_id', explode(",",$company['ad_account_id']));
        $facebookBuilder->where('G.id', $data['aat_id']);
        $facebookBuilder->groupBy('G.id');

        return $facebookBuilder;
    }

    private function getGoogleByCompany($data, $company)
    {
        $googleBuilder = $this->zenith->table('z_adwords.aw_ad_report_history A');
        $googleBuilder->select('
            SUM(D.amount) AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.cost) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.impressions) AS impressions,
            SUM(A.clicks) AS click,
        ');
        $googleBuilder->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
        $googleBuilder->join('z_adwords.aw_adgroup C', 'B.adgroupId = C.id');
        $googleBuilder->join('z_adwords.aw_campaign D', 'C.campaignId = D.id');
        $googleBuilder->join('z_adwords.aw_ad_account E', 'D.customerId = E.customerId');
        $googleBuilder->join('zenith.company_adaccounts F', 'E.customerId = F.ad_account_id AND F.media = "google"');
        $googleBuilder->join('zenith.companies G', 'F.company_id = G.id');
        $googleBuilder->where('D.status !=', 'NODATA');
        $googleBuilder->where('DATE(A.date) >=', date('Y-m-d'));
        $googleBuilder->where('DATE(A.date) <=', date('Y-m-d'));
        $googleBuilder->whereIn('E.customerId', explode(",",$company['ad_account_id']));
        $googleBuilder->where('G.id', $data['aat_id']);
        $googleBuilder->groupBy('G.id');

        return $googleBuilder;
    }

    private function getKakaoByCompany($data, $company)
    {
        $kakaoBuilder = $this->zenith->table('z_moment.mm_creative_report_basic A');
        $kakaoBuilder->select('
            SUM(D.dailyBudgetAmount) AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.cost) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.imp) AS impressions,
            SUM(A.click) AS click,
        ');
        $kakaoBuilder->join('z_moment.mm_creative B', 'A.id = B.id');
        $kakaoBuilder->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
        $kakaoBuilder->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
        $kakaoBuilder->join('z_moment.mm_ad_account E', 'D.ad_account_id = E.id');
        $kakaoBuilder->join('zenith.company_adaccounts F', 'E.id = F.ad_account_id AND F.media = "kakao"');
        $kakaoBuilder->join('zenith.companies G', 'F.company_id = G.id');
        $kakaoBuilder->where('DATE(A.date) >=', date('Y-m-d'));
        $kakaoBuilder->where('DATE(A.date) <=', date('Y-m-d'));
        $kakaoBuilder->whereIn('E.id', explode(",",$company['ad_account_id']));
        $kakaoBuilder->where('G.id', $data['aat_id']);
        $kakaoBuilder->groupBy('G.id');

        return $kakaoBuilder;
    }

    public function getTargetFacebook($data)
    {
        $builder = $this->zenith->table('z_facebook.fb_ad_insight_history A');
        $builder->select(' 
            SUM(D.budget) AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.spend) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.impressions) AS impressions,
            SUM(A.inline_link_clicks) AS click,
        ');
        $builder->join('z_facebook.fb_ad B', 'A.ad_id = B.ad_id');
        $builder->join('z_facebook.fb_adset C', 'B.adset_id = C.adset_id');
        $builder->join('z_facebook.fb_campaign D', 'C.campaign_id = D.campaign_id');
        $builder->join('z_facebook.fb_ad_account E', 'D.account_id = E.ad_account_id');
        /* $builder->where('DATE(A.date) >=', date('Y-m-d'));
        $builder->where('DATE(A.date) <=', date('Y-m-d')); */
        switch ($data['aat_type']) {
            case 'account':
                $builder->select('E.status AS status');
                $builder->where('E.ad_account_id', $data['aat_id']);      
                $builder->groupBy('E.ad_account_id');      
                break;
            case 'campaign':
                $builder->select('D.status AS status');
                $builder->where('D.campaign_id', $data['aat_id']);
                $builder->groupBy('D.campaign_id');  
                break;
            case 'adgroup':                
                $builder->select('C.status AS status');
                $builder->where('C.adset_id', $data['aat_id']);
                $builder->groupBy('C.adset_id');  
                break;
            case 'ad':
                $builder->select('B.status AS status');
                $builder->where('B.ad_id', $data['aat_id']);
                $builder->groupBy('B.ad_id');  
                break;
            default:
                return;
        }
       
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getTargetGoogle($data)
    {
        $builder = $this->zenith->table('z_adwords.aw_ad_report_history A');
        $builder->select('
            SUM(D.amount) AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.cost) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.impressions) AS impressions,
            SUM(A.clicks) AS click,
        ');
        $builder->join('z_adwords.aw_ad B', 'A.ad_id = B.id');
        $builder->join('z_adwords.aw_adgroup C', 'B.adgroupId = C.id');
        $builder->join('z_adwords.aw_campaign D', 'C.campaignId = D.id');
        $builder->join('z_adwords.aw_ad_account E', 'D.customerId = E.customerId');
        /* $builder->where('DATE(A.date) >=', date('Y-m-d'));
        $builder->where('DATE(A.date) <=', date('Y-m-d')); */
        switch ($data['aat_type']) {
            case 'account':
                $builder->select('E.status AS status');
                $builder->where('E.customerId', $data['aat_id']);  
                $builder->groupBy('E.customerId');          
                break;
            case 'campaign':
                $builder->select('D.status AS status');
                $builder->where('D.id', $data['aat_id']);
                $builder->groupBy('D.id');
                break;
            case 'adgroup':
                $builder->select('C.status AS status');
                $builder->where('C.id', $data['aat_id']);
                $builder->groupBy('C.id');
                break;
            case 'ad':
                $builder->select('B.status AS status');
                $builder->where('B.id', $data['aat_id']);
                $builder->groupBy('B.id');
                break;
            default:
                return;
        }
        
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getTargetKakao($data)
    {
        $builder = $this->zenith->table('z_moment.mm_creative_report_basic A');
        $builder->select('
            SUM(D.dailyBudgetAmount) AS budget,
            SUM(A.db_count) AS unique_total,
            SUM(A.cost) AS spend,
            SUM(A.margin) AS margin,
            SUM(A.sales) AS sales,
            SUM(A.imp) AS impressions,
            SUM(A.click) AS click,
        ');
        $builder->join('z_moment.mm_creative B', 'A.id = B.id');
        $builder->join('z_moment.mm_adgroup C', 'B.adgroup_id = C.id');
        $builder->join('z_moment.mm_campaign D', 'C.campaign_id = D.id');
        $builder->join('z_moment.mm_ad_account E', 'D.ad_account_id = E.id');
        /* $builder->where('DATE(A.date) >=', date('Y-m-d'));
        $builder->where('DATE(A.date) <=', date('Y-m-d')); */
        switch ($data['aat_type']) {
            case 'account':
                $builder->select('E.config AS status');
                $builder->where('E.id', $data['aat_id']);            
                $builder->groupBy('E.id');
                break;
            case 'campaign':
                $builder->select('D.config AS status');
                $builder->where('D.id', $data['aat_id']);
                $builder->groupBy('D.id');
                break;
            case 'adgroup':
                $builder->select('C.config AS status');
                $builder->where('C.id', $data['aat_id']);
                $builder->groupBy('C.id');
                break;
            case 'ad':
                $builder->select('B.config AS status');
                $builder->where('B.id', $data['aat_id']);
                $builder->groupBy('B.id');
                break;
            default:
                return;
        }
        
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function getAutomationConditionBySeq($seq)
    {
        $builder = $this->zenith->table('aa_conditions');
        $builder->select('*');
        $builder->where('idx', $seq);
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
