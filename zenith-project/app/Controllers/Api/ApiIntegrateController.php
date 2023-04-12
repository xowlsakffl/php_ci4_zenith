<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\Api\IntegrateModel;
use CodeIgniter\API\ResponseTrait;

class ApiIntegrateController extends BaseController
{
    use ResponseTrait;
    
    protected $integrate;
    public function __construct() 
    {
        $this->integrate = model(IntegrateModel::class);
    }

    public function index()
    {
        return view('integrate/management');
    }

    public function getList()
    {

        //if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $param = [
                'length' => $this->request->getGet('length'),
                'start' => $this->request->getGet('start'),
                'draw' => $this->request->getGet('draw'),
                'sdate' => $this->request->getGet('sdate'),
                'edate' => $this->request->getGet('edate'),
                'stx' => $this->request->getGet('stx'),
                'adv_seq' => $this->request->getGet('adv_seq'),
                'media' => $this->request->getGet('media'),
                'event' => $this->request->getGet('event'),
            ];

            $result = $this->integrate->getEventLead($param);

            foreach($result['data'] as &$row){
                $etc = [];
                if(!empty($row['email'])) {
                    $etc[] = $row['email'];
                }
                for($i2=1;$i2<6;$i2++){
                    if(!empty($row['add'.$i2])){		
                        //2020109 정문숙 / 기타에 파일경로가 있으면 파일보기로 설정
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

            

            $result = [
                'data' => $result['data'],
                'recordsTotal' => $result['allCount'],
                'recordsFiltered' => $result['allCount'],
                'draw' => intval($param['draw']),
            ];

            return $this->respond($result);
        //}else{
            return $this->fail("잘못된 요청");
        //}
    }

    public function getEventLeadCount()
    {

        //if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $param = [
                'sdate' => $this->request->getGet('sdate'),
                'edate' => $this->request->getGet('edate'),
                'stx' => $this->request->getGet('stx'),
                'adv_seq' => $this->request->getGet('adv_seq'),
                'media' => $this->request->getGet('media'),
                'event' => $this->request->getGet('event'),
            ];

            $result = $this->integrate->getEventLeadCount($param);

            return $this->respond($result);
        //}else{
            return $this->fail("잘못된 요청");
        //}
    }

    public function getStatusCount()
    {

        //if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $param = [
                'sdate' => $this->request->getGet('sdate'),
                'edate' => $this->request->getGet('edate'),
                'stx' => $this->request->getGet('stx'),
                'adv_seq' => $this->request->getGet('adv_seq'),
                'media' => $this->request->getGet('media'),
                'event' => $this->request->getGet('event'),
            ];

            $result = $this->integrate->getStatusCount($param);

            return $this->respond($result);
        //}else{
            return $this->fail("잘못된 요청");
        //}
    }

    public function getLead()
    {
        $param = [
            'sdate' => $this->request->getGet('sdate'),
            'edate' => $this->request->getGet('edate'),
            'stx' => $this->request->getGet('stx'),
        ];
        $adv = $this->integrate->getAdvertiser($param);
        $media = $this->integrate->getMedia($param);
        $event = $this->integrate->getEvent($param);

        $result = [
            'adv' => $adv,
            'media' => $media,
            'event' => $event,
        ];

        return $this->respond($result);
    }

}
