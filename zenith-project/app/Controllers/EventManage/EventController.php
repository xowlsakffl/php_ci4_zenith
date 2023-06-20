<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;
use App\Models\EventManage\EventModel;
use CodeIgniter\API\ResponseTrait;

class EventController extends BaseController
{
    use ResponseTrait;
    
    protected $event;
    public function __construct() 
    {
        $this->event = model(EventModel::class);
    }
    
    public function index()
    {
        return view('events/event/event');
    }

    public function getData()
    {
        if(/* $this->request->isAJAX() &&  */strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->event->getInformation($arg);
            $ads = $this->event->getEnabledAds();
            $issues = $this->event->getIssuesFromMantis();
            $list = $result['data'];
            for ($i = 0; $i < count($list); $i++) {   
                if($list[$i]['is_stop']){
                    $list[$i]['is_stop'] = '사용중지';
                }else{
                    $list[$i]['is_stop'] = '사용중';
                }

                if($list[$i]['interlock']){
                    $list[$i]['interlock'] = 'O';
                }

                if($list[$i]['lead']){
                    $lead_array = array("0" => $list[$i]['title'], "1" => "잠재고객", "2" => "엑셀업로드", "3" => "API 수신", "4" => "카카오 비즈폼");
                    $list[$i]['title'] = $lead_array[$list[$i]['lead']];
                }

                $list[$i]['mantis'] = [];
                if ($issues[$list[$i]['seq']]['id']) {
                    $list[$i]['mantis']['id'] = $issues[$list[$i]['seq']]['id'];
                    if ($issues[$list[$i]['seq']]['designer'])
                        $list[$i]['mantis']['designer'] = $issues[$list[$i]['seq']]['designer'];
                    if ($issues[$list[$i]['seq']]['developer'])
                        $list[$i]['mantis']['developer'] = $issues[$list[$i]['seq']]['developer'];
                }

                if(preg_match('/(카카오|GDN|페이스북|잠재|유튜브)/', $list[$i]['media_name'])) {
                    $is_enabledAds = false;
                    $list[$i]['config'] = 'disabled';
                    if(in_array($list[$i]['seq'], $ads)){
                        $is_enabledAds = true;
                    }
                    if($is_enabledAds) {
                        $list[$i]['config'] = 'enabled';
                    }
                }
            }

            $result = [
                'data' => $list,
                'recordsTotal' => $result['allCount'],
                'recordsFiltered' => $result['allCount'],
                'draw' => intval($arg['draw']),
            ];

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
