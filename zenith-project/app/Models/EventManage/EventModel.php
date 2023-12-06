<?php

namespace App\Models\EventManage;

use CodeIgniter\Model;

class EventModel extends Model
{
	public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->facebook = \Config\Database::connect('facebook');
        $this->google = \Config\Database::connect('google');
        $this->kakao = \Config\Database::connect('kakao');
    }

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

        $builder->groupBy('info.seq');

        // limit 적용하지 않은 쿼리
        $builderNoLimit = clone $builder;

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
		if($this->facebook->getConnection() == false || $this->google->getConnection() == false || $this->kakao->getConnection() == false){
			return false;
		}

        $fbSubQuery = $this->db->table('z_facebook.fb_ad AS ad');
        $fbSubQuery->select('ad.ad_name AS name, cr.link AS link');
        $fbSubQuery->join('z_facebook.fb_adcreative AS cr', 'cr.ad_id = ad.ad_id', 'left');
        $fbSubQuery->where('ad.effective_status', 'ACTIVE');
        $fbSubQuery->groupStart();
        $fbSubQuery->like('ad.ad_name', '#');
        $fbSubQuery->orGroupStart();
        $fbSubQuery->where('cr.link !=', '');
        $fbSubQuery->groupEnd();
        $fbSubQuery->groupEnd();

        $awSubQuery = $this->db->table('z_adwords.aw_ad AS ad');
        $awSubQuery->select("IF(INSTR(code, '#')>0, ad.code, ad.name) AS name, ad.finalUrl AS link");
        $awSubQuery->join('z_adwords.aw_adgroup AS ag', 'ad.adgroupId = ag.id', 'left');
        $awSubQuery->join('z_adwords.aw_campaign AS ac', 'ag.campaignId = ac.id', 'left');
        $awSubQuery->where('ad.status', 'ENABLED');
        $awSubQuery->where('ag.status', 'ENABLED');
        $awSubQuery->where('ac.status', 'ENABLED');
        $awSubQuery->groupStart();
        $awSubQuery->like('ad.name', '#');
        $awSubQuery->orLike('ad.code', '#');
        $awSubQuery->orGroupStart();
        $awSubQuery->where('ad.finalUrl !=', '');
        $awSubQuery->groupEnd();
        $awSubQuery->groupEnd();

        $mmSubQuery = $this->db->table('z_moment.mm_creative AS ad');
        $mmSubQuery->select('ad.name, ad.landingUrl AS link');
        $mmSubQuery->join('z_moment.mm_adgroup AS ag', 'ad.adgroup_id = ag.id', 'left');
        $mmSubQuery->join('z_moment.mm_campaign AS ac', 'ag.campaign_id = ac.id', 'left');
        $mmSubQuery->where('ad.config', 'ON');
        $mmSubQuery->where('ag.config', 'ON');
        $mmSubQuery->where('ac.config', 'ON');
        $mmSubQuery->groupStart();
        $mmSubQuery->like('ad.name', '#');
        $mmSubQuery->orGroupStart();
        $mmSubQuery->where('ad.landingUrl !=', '');
        $mmSubQuery->groupEnd();
        $mmSubQuery->groupEnd();

        $fbSubQuery->union($awSubQuery)->union($mmSubQuery);
        $resultQuery = $this->db->newQuery()->fromSubquery($fbSubQuery, 'tb');
        $resultQuery->where("tb.name REGEXP('^.*#([0-9]+).*') OR tb.link REGEXP('.+(kr|com|event)\/([0-9]+)')");
        $result = $resultQuery->get()->getResultArray();
        
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
        $this->db->transStart();
        $keyword = $data['keyword'];
        unset($data['keyword']);
        $builder = $this->db->table('event_information');
        $builder->insert($data);
        $seq = $this->db->insertID();
        $event = $builder->getWhere(['seq' => $seq])->getRowArray();
        $result = $this->db->transComplete();
        
        if(!empty($event)){
            $this->syncKeyword($keyword, $event['seq']);
            
            return $result;
        }
    }

    public function updateEvent($data, $seq)
    {
        $this->db->transStart();
        $keyword = $data['keyword'];
        unset($data['keyword']);
        $builder = $this->db->table('event_information');
        $builder->where('seq', $seq);
        $builder->update($data);
        $result = $this->db->transComplete();
        if(!empty($result)){
            $this->syncKeyword($keyword, $seq);
            
            return $result;
        }
    }

    public function copyEvent($seq)
    {
        $this->db->transStart();
        $data = $this->db->table('event_information')->select('advertiser, media, lead, title, description, subtitle, object, object_items, interlock, partner_id, partner_name, paper_code, paper_name, pixel_id, view_script, done_script, db_price, check_gender, check_age_min, check_age_max, duplicate_term, check_phone, check_name, check_cookie')
            ->where('seq', $seq)
            ->get()->getRowArray();

        $data['is_stop'] = 0;
        $data['username'] = auth()->user()->username;
        $data['ei_datetime'] = date('Y-m-d H:i:s');

        $this->db->table('event_information')->insert($data);
        $result = $this->db->transComplete();
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

    public function getEventImpressions($seq)
    {
        $builder = $this->db->table('event_impressions_history');
        $builder->select('seq, code, site, SUM(impressions) AS impressions');
        $builder->where('seq', $seq);
        $builder->groupBy(['seq', 'code', 'site']);

        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function deleteEvent($seq)
    {
        $this->db->transStart();
        $builder = $this->db->table('event_information');
        $builder->where('seq', $seq);
        $result = $builder->delete();
        $result = $this->db->transComplete();
        return $result;
    }

    public function syncKeyword($keywords, $seq)
    {
        $this->db->transStart();
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
        $result = $this->db->transComplete();
        return $result;
    }
}
