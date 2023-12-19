<?php

namespace App\Controllers\Integrate;

use App\Controllers\BaseController;
use App\Models\Integrate\IntegrateModel;
use CodeIgniter\API\ResponseTrait;

class IntegrateController extends BaseController
{
    use ResponseTrait;
    
    protected $integrate;
    protected $is_pravate_perm = false;
    public function __construct() 
    {
        $this->integrate = model(IntegrateModel::class);
        if(auth()->user()->inGroup('superadmin','admin','developer', 'agency', 'advertiser')) {
            $this->is_pravate_perm = true;
        }
    }

    public function index()
    {
        $_get = $this->request->getGet();
        return view('integrate/management', $_get);
    }

    public function getList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $event_url = getenv('EVENT_SERVER_URL');
            if(!isset($arg['searchData'])) {
                $arg['searchData'] = [
                    'sdate'=> date('Y-m-d'),
                    'edate'=> date('Y-m-d')
                ];
            }
            //list
            $list = $this->integrate->getEventLead($arg);
            foreach($list['data'] as &$d){
                $etc = [];
                if(!empty($d['email'])) {
                    $etc[] = $d['email'];
                }
                if(!$this->is_pravate_perm) {
                    if(!preg_match('/test|테스트/i', $d['name']) && $d['status'] != 7) {
                        $d['dec_phone'] = substr($d['dec_phone'],0,3).'<i class="masking">&#9618;&#9618;</i>'.substr($d['dec_phone'],-4);
                        $d['dec_phone'] = html_entity_decode($d['dec_phone']);
                        $name = trim($d['name']);
                        $d['name'] = '';
                        for($ii=0; $ii<mb_strlen($name); $ii++) {
                            if(mb_strlen($name) >= 4 && $ii > 2 && mb_strlen($name)-1 != $ii)
                                continue;
                            else 
                                $d['name'] .= (($ii>0&&$ii<mb_strlen($name)-1)||$ii==1)?'<i class="masking">&#9618;</i>':mb_substr($name, $ii, 1);
                                $d['name'] = html_entity_decode($d['name']);
                        }
                    }
                }
                for($i2=1;$i2<6;$i2++){
                    if(!empty($d['add'.$i2])){		
                        if(strpos($d['add'.$i2], "uploads")){
                            $href = "<a href='".str_replace("./","{$event_url}uploads/",$d['add'.$i2])."' target='_blank'>[파일보기]</a>";
                            $etc[] = $href;
                        }else if(strpos($d['add'.$i2], "/v_")){
                            $href = "<a href='{$event_url}img_viewer.php?data={$d['add'.$i2]}' target='_blank'>[파일보기]</a>";
                            $etc[] = $href;
                        }
                        else{
                            $etc[] = $d['add'.$i2];
                        }
                    }
                }
                if(!empty($d['memo'])){
                    $etc[] = $d['memo'];
                }
                if(!empty($d['win'])){
                    if($d['win']!="등"){
                        $etc[] = $d['win'];
                    }
                }
                if(!empty($d['addr'])){
                    $etc[] = $d['addr'];
                }
                if(!empty($d['branch'])){
                    $etc[] = $d['branch'];				
                }
                $add = implode('/', $etc);    
                $d['add'][] = $add;
            }
            if(isset($arg['noLimit'])) {
                return $this->respond($list['data']);
            }

            $leadsAll = $this->integrate->getEventLeadCount($arg);

            $buttons['filtered'] = $this->setCount($leadsAll['filteredResult'], 'count');
            $buttons['noFiltered'] = $this->setCount($leadsAll['noFilteredResult'], 'total');
            foreach($buttons['noFiltered'] as $type => $row){
                foreach($row as $k => $v) {
                    if(!isset($buttons['filtered'][$type][$k]))
                        $buttons['filtered'][$type][$k] = ['count' => 0];
                    $filter_lists[$type][$k] = array_merge($buttons['filtered'][$type][$k], $v);
                }                
            }
            if(!empty($filter_lists)){
                foreach($filter_lists as $type => $row) {
                    $sortedRow = array();

                    foreach ($row as $name => $v) {
                        $sortedRow[$name] = array_merge(['label' => $name], $v);
                    }
                
                    asort($sortedRow);
                    $sortedRow = array_values($sortedRow);
                
                    $filters[$type] = $sortedRow;
                }
            }
            $status = $this->integrate->getStatusCount($arg);
            $filters['status'] = $status;
            /* $buttons = [
                'advertiser' => $data['advertiser'],
                'media' => $data['media'],
                'event' => $data['event'],
                'status' => $status,
            ]; */
            
            $result = [
                'data' => $list['data'],
                'recordsTotal' => $list['allCount'],
                'recordsFiltered' => $list['allCount'],
                'draw' => intval($arg['draw']),
                'buttons' => $filters,
            ];
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getEventLeadCount()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            if(!isset($arg['searchData'])) {
                $arg['searchData'] = [
                    'sdate'=> date('Y-m-d'),
                    'edate'=> date('Y-m-d')
                ];
            }
            $data = $this->integrate->getEventLeadCount($arg);

            $adv_counts = array();
            $med_counts = array();
            $event_counts = array();
            foreach ($data as $row) {
                if (!array_key_exists($row['adv_name'], $adv_counts)) {
                    $adv_counts[$row['adv_name']] = array(
                        'countAll' => 0
                    );
                }

                if (!array_key_exists($row['med_name'], $med_counts)) {
                    $med_counts[$row['med_name']] = array(
                        'countAll' => 0
                    );
                }

                $event_counts[$row['event']] = array(
                    'countAll' => $row['countAll'],
                );

                $adv_counts[$row['adv_name']]['countAll'] += $row['countAll'];
                $med_counts[$row['med_name']]['countAll'] += $row['countAll'];
            }

            $result = [
                'advertiser' => $adv_counts,
                'media' => $med_counts,
                'event' => $event_counts,
            ];
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getStatusCount()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            if(!isset($arg['searchData'])) {
                $arg['searchData'] = [
                    'sdate'=> date('Y-m-d'),
                    'edate'=> date('Y-m-d')
                ];
            }
            $result = $this->integrate->getStatusCount($arg);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function getMemo()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->integrate->getMemo($arg);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function addMemo() {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $arg = $this->request->getPost();
            $result = $this->integrate->addMemo($arg);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function setStatus() {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            if(!auth()->user()->hasPermission('integrate.status')){
                return $this->fail("권한이 없습니다.");
            }
            $arg = $this->request->getPost();
            $result = $this->integrate->setStatus($arg);
                
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    private function setCount($leads, $type)
    {
        $data = [
            'advertiser' => [],
            'media' => [],
            'event' => [],
        ];
        foreach($leads as $row) {
            //광고주 기준
            if (!empty($row['advertiser'])) {
                if (!isset($data['advertiser'][$row['advertiser']])) {
                    $data['advertiser'][$row['advertiser']][$type] = 0;
                }

                if($row['status'] == 1){
                    $data['advertiser'][$row['advertiser']][$type]++;
                }
            }

            //매체 기준
            if (!empty($row['media'])) {
                if (!isset($data['media'][$row['media']])) {
                    $data['media'][$row['media']][$type] = 0;
                }

                if($row['status'] == 1){
                    $data['media'][$row['media']][$type]++;
                }
            }

            //이벤트 기준
            if (!empty($row['event'])) {
                if (!isset($data['event'][$row['event']])) {
                    $data['event'][$row['event']][$type] = 0;
                }

                if($row['status'] == 1){
                    $data['event'][$row['event']][$type]++;
                }
            }
        }

        return $data;
    }
}
