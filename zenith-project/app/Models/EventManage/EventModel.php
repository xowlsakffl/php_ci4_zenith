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
        $builder->join('(SELECT seq, SUM(db_count) AS db_count FROM event_dbcount_history GROUP BY seq) AS db', 'info.seq = db.seq', 'left');
        $builder->join('event_advertiser AS adv', 'info.advertiser = adv.seq', 'left');
        $builder->join('event_media AS med', 'info.media = med.seq', 'left');
        $builder->join('event_impressions_history AS imp', 'info.seq = imp.seq', 'left');

        /* $builder->where('DATE(info.ei_datetime) >=', $srch['sdate']);
        $builder->where('DATE(info.ei_datetime) <=', $srch['edate']); */

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
}
