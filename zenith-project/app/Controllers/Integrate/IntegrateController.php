<?php

namespace App\Controllers\Integrate;

use App\Controllers\BaseController;
use App\Models\Integrate\IntegrateModel;
use CodeIgniter\API\ResponseTrait;

class IntegrateController extends BaseController
{
    use ResponseTrait;
    
    protected $integrate;
    public function __construct() 
    {
        $this->integrate = model(IntegrateModel::class);
    }

    public function index()
    {
        $_get = $this->request->getGet();
        return view('integrate/management', $_get);
    }

    public function getList()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            if(!isset($arg['searchData'])) {
                $arg['searchData'] = [
                    'sdate'=> date('Y-m-d'),
                    'edate'=> date('Y-m-d')
                ];
            }
            //list
            $result = $this->integrate->getEventLead($arg);

            foreach($result['data'] as &$row){
                $etc = [];
                if(!empty($row['email'])) {
                    $etc[] = $row['email'];
                }
                for($i2=1;$i2<6;$i2++){
                    if(!empty($row['add'.$i2])){		
                        if(strpos($row['add'.$i2], "uploads")){
                            $href = "<a href='".str_replace("./","https://event.hotblood.co.kr/uploads/",$row['add'.$i2])."' target='_blank'>[파일보기]</a>";
                            $etc[] = $href;
                        }else if(strpos($row['add'.$i2], "/v_")){
                            $href = "<a href='https://event.hotblood.co.kr/img_viewer.php?data={$row['add'.$i2]}' target='_blank'>[파일보기]</a>";
                            $etc[] = $href;
                        }
                        else{
                            $etc[] = $row['add'.$i2];
                        }
                    }
                }
                if(!empty($row['memo'])){
                    $etc[] = $row['memo'];
                }
                if(!empty($row['win'])){
                    if($row['win']!="등"){
                        $etc[] = $row['win'];
                    }
                }
                if(!empty($row['addr'])){
                    $etc[] = $row['addr'];
                }
                if(!empty($row['branch'])){
                    $etc[] = $row['branch'];				
                }
                $add = implode('/', $etc);    
                $row['add'][] = $add;
            }

            $data = $this->integrate->getEventLeadCount($arg);
            $status = $this->integrate->getStatusCount($arg);

            $adv_counts = array();
            $med_counts = array();
            $event_counts = array();
            foreach ($data as $d) {
                if (!array_key_exists($d['adv_name'], $adv_counts)) {
                    $adv_counts[$d['adv_name']] = array(
                        'countAll' => 0
                    );
                }

                if (!array_key_exists($d['med_name'], $med_counts)) {
                    $med_counts[$d['med_name']] = array(
                        'countAll' => 0
                    );
                }

                $event_counts[$d['event']] = array(
                    'countAll' => $d['countAll'],
                );

                $adv_counts[$d['adv_name']]['countAll'] += $d['countAll'];
                $med_counts[$d['med_name']]['countAll'] += $d['countAll'];
            }

            $buttons = [
                'advertiser' => $adv_counts,
                'media' => $med_counts,
                'event' => $event_counts,
                'status' => $status,
            ];

            $result = [
                'data' => $result['data'],
                'recordsTotal' => $result['allCount'],
                'recordsFiltered' => $result['allCount'],
                'draw' => intval($arg['draw']),
                'buttons' => $buttons,
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
            $arg = $this->request->getPost();
            $result = $this->integrate->setStatus($arg);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
