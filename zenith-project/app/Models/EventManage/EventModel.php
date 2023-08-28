<?php

namespace App\Models\EventManage;

use CodeIgniter\Model;

class EventModel extends Model
{
    public function getInformation($data)
    {
        $srch = $data['searchData'];
        $builder = $this->db->table('event_information AS info');
        $builder->select('IFNULL(db.db_count, 0) AS db, adv.name AS advertiser_name, med.media AS media_name, SUM(imp.impressions) AS impressions, info.*');
        $builder->join('(SELECT seq, SUM(db_count) AS db_count FROM event_leads_count GROUP BY seq) AS db', 'info.seq = db.seq', 'left');
        $builder->join('event_advertiser AS adv', 'info.advertiser = adv.seq', 'left');
        $builder->join('event_media AS med', 'info.media = med.seq', 'left');
        $builder->join('event_impressions_history AS imp', 'info.seq = imp.seq', 'left');

        if(!empty($srch['sdate']) && !empty($srch['edate'])){
            $builder->where('DATE(info.ei_datetime) >=', $srch['sdate']);
            $builder->where('DATE(info.ei_datetime) <=', $srch['edate']);
        }
        
        if(!empty($srch['stx'])){
            $builder->groupStart();
            $builder->like('info.title', $srch['stx']);
            $builder->orLike('adv.name', $srch['stx']);
            $builder->orLike('med.media', $srch['stx']);
            $builder->orLike('info.description', $srch['stx']);
            $builder->orLike('info.seq', $srch['stx']);
            $builder->groupEnd();
        }

        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

        $builder->groupBy('info.seq');
        $orderBy = [];
        if(!empty($data['order'])) {
            foreach($data['order'] as $row) {
                $col = $data['columns'][$row['column']]['data'];
                if($col) $orderBy[] = "{$col} {$row['dir']}";
            }
        }
        $orderBy[] = "info.seq DESC";
        $builder->orderBy(implode(",", $orderBy),'',true);
        if($data['length'] > 0) $builder->limit($data['length'], $data['start']);
        // dd($builder->getCompiledSelect());
        // 결과 반환
        $result = $builder->get()->getResultArray();
        $resultNoLimit = $builderNoLimit->countAllResults();

        return [
            'data' => $result,
            'allCount' => $resultNoLimit
        ];
    }

    public function getIssuesFromMantis()
	{
        $builder = $this->db->table('mantis.mantis_bug_table AS A');
        $builder->select('A.id, B.text, D.realname AS developer, C.value AS designer');
        $builder->join('mantis.mantis_custom_field_string_table AS B', 'A.id = B.bug_id AND B.field_id = "20" AND B.text LIKE "%//%"', 'left');
        $builder->join('mantis.mantis_custom_field_string_table AS C', 'A.id = C.bug_id AND C.field_id = "10"', 'left');
        $builder->join('mantis.mantis_user_table AS D', 'A.handler_id = D.id', 'left');
        $builder->where('B.text IS NOT NULL');

        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getEnabledAds()
	{
        $query = $this->db->query("SELECT * FROM
        (
        (SELECT ad.ad_name AS name, cr.link AS link FROM `facebook`.`fb_ad` AS ad
            LEFT JOIN `facebook`.`fb_adcreative` AS cr ON cr.ad_id = ad.ad_id
        WHERE ad.effective_status = 'ACTIVE' AND (ad.ad_name LIKE '%#%' OR cr.link <> ''))
        UNION
        (SELECT IF(INSTR(code, '#')>0, ad.code, ad.name) AS name, ad.finalUrl AS link 
            FROM `adwords`.`aw_ad` AS ad
            LEFT JOIN `adwords`.`aw_adgroup` AS ag ON ad.adgroupId = ag.id
            LEFT JOIN `adwords`.`aw_campaign` AS ac ON ag.campaignId = ac.id
        WHERE ad.status = 'ENABLED' AND ag.status = 'ENABLED' AND ac.status = 'ENABLED' AND (ad.name LIKE '%#%' OR ad.code LIKE '%#%' OR ad.finalUrl <> ''))
        UNION
        (SELECT ad.name, ad.landingUrl AS link
            FROM `moment`.`mm_creative` AS ad
            LEFT JOIN `moment`.`mm_adgroup` AS ag ON ad.adgroup_id = ag.id
            LEFT JOIN `moment`.`mm_campaign` AS ac ON ag.campaign_id = ac.id
        WHERE ad.config = 'ON' AND ag.config = 'ON' AND ac.config = 'ON' AND (ad.name LIKE '%#%' OR ad.landingUrl <> ''))
        ) AS tb
        WHERE tb.name REGEXP('^.*#([0-9]+).*') OR tb.link REGEXP('.+(kr|com|event)\/([0-9]+)')");

        $result = $query->getResultArray();

        foreach($result as $row) {
			if(preg_match('/^.+(kr|com|event)\/([0-9]{3,6})(\?.+)?$/', $row['link'])) {
				$data[] = preg_replace('/^.+(kr|com|event)\/([0-9]{3,6})(\?.+)?/', '$2', $row['link']);
			} else if(preg_match('/^.*\#([0-9]+).*/', $row['name'])) {
				$data[] = preg_replace('/^.*\#([0-9]+).*/', '$1', $row['name']);
			}
		}
		$data = array_unique($data);
        return $data;
    }

    public function getAdv($stx)
    {
        $builder = $this->db->table('event_advertiser');
        $builder->select('seq as id, name as value');
        if(!empty($stx)){
            $builder->like('name', $stx);
        }

        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getMedia($stx)
    {
        $builder = $this->db->table('event_media');
        $builder->select('seq as id, media as value');
        if(!empty($stx)){
            $builder->like('media', $stx);
        }

        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function createEvent($data)
    {
        $keyword = $data['keyword'];
        unset($data['keyword']);
        $builder = $this->db->table('event_information');
        $builder->insert($data);
        $result = $builder->getWhere(['seq' => $this->db->insertID()])->getRowArray();
        if(!empty($result)){
            $this->syncKeyword($keyword, $result['seq']);
            
            return true;
        }
    }

    public function updateEvent($data, $seq)
    {
        $keyword = $data['keyword'];
        unset($data['keyword']);
        $builder = $this->db->table('event_information');
        $builder->where('seq', $seq);
        $result = $builder->update($data);
        if(!empty($result)){
            $this->syncKeyword($keyword, $seq);
            
            return true;
        }
    }

    public function copyEvent($seq)
    {
        $data = $this->db->table('event_information')->select('advertiser, media, lead, title, description, subtitle, object, object_items, interlock, partner_id, partner_name, paper_code, paper_name, pixel_id, view_script, done_script, db_price, check_gender, check_age_min, check_age_max, duplicate_term, check_phone, check_name, check_cookie')
            ->where('seq', $seq)
            ->get()->getRowArray();

        $data['is_stop'] = 0;
        $data['username'] = auth()->user()->username;
        $data['ei_datetime'] = date('Y-m-d H:i:s');

        $result = $this->db->table('event_information')->insert($data);

        return $result;
    }

    public function getEvent($seq)
    {
        $builder = $this->db->table('event_information AS info');
        $builder->select('info.*, adv.name AS advertiser_name, med.media AS media_name, GROUP_CONCAT(ek.keyword) AS keywords');
        $builder->join('event_advertiser AS adv', 'info.advertiser = adv.seq', 'left');
        $builder->join('event_media AS med', 'info.media = med.seq', 'left');
        $builder->join('event_keyword_idx AS ki', 'info.seq = ki.ei_seq', 'left');
        $builder->join('event_keyword AS ek', 'ki.ek_seq = ek.seq', 'left');
        $builder->where('info.seq', $seq);
        
        $result = $builder->get()->getRowArray();
        return $result;
    }

    public function deleteEvent($seq)
    {
        $builder = $this->db->table('event_information');
        $builder->where('seq', $seq);
        $result = $builder->delete();

        return $result;
    }

    public function syncKeyword($keywords, $seq)
    {
        $builder_3 = $this->db->table('event_keyword_idx');
        $builder_3->where('ei_seq', $seq);
        $builder_3->delete();

        if(!empty($keywords)){
            $keywords = explode(',', $keywords);
            foreach ($keywords as $keyword) {
                $builder_1 = $this->db->table('event_keyword');
                $builder_1->select('seq, keyword');
                $builder_1->where('keyword', $keyword);
                $existRow = $builder_1->get()->getRowArray();
    
                if(empty($existRow)){
                    $builder_2 = $this->db->table('event_keyword');
                    $builder_2->set('keyword', $keyword);
                    $builder_2->insert();
                    $existRow = $builder_2->getWhere(['seq' => $this->db->insertID()])->getRowArray();
                }
    
                $builder_4 = $this->db->table('event_keyword_idx');
                $builder_4->set('ei_seq', $seq);
                $builder_4->set('ek_seq', $existRow['seq']);
                $builder_4->insert();
                
            }
        }

        return true;
    }
}
