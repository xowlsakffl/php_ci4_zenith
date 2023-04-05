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
            $param = $this->request->getGet();
            $param['page'] = $param['page'] ?? 1;
            $param['limit'] = $param['limit'] ?? 20;
            $param['sort_by']  = $param['sort_by'] ?? 'seq';
            $param['sort_order'] = $param['sort_order'] ?? 'desc';

            $param['sdate'] = '2023-04-03';
            $param['edate'] = '2023-04-04';

            $result = $this->integrate->getEventLead($param);

            /* $data = [
                'advertiser'=>['count'=>[],'list'=>[],'media'=>[],'tab'=>[]],
                'media'=>['count'=>[],'list'=>[],'media'=>[],'tab'=>[]],
                'tab'=>['count'=>[],'list'=>[],'media'=>[],'tab'=>[]],
                'stat'=>[
                    '전체'=>0, '인정'=>0, '중복'=>0, '성별불량'=>0, '나이불량'=>0, '콜불량'=>0, '번호불량'=>0, '테스트'=>0, '이름불량'=>0, '지역불량'=>0, '업체불량'=>0, '미성년자'=>0, '본인아님'=>0, '확인'=>0
                ],
                'data'=>[]
            ];
            if($result['dataNoLimit']){
                $no = count($result['dataNoLimit']);
                foreach ($result['dataNoLimit'] as $row) {
                    $row['no'] = $no--;
                    if($row['status'] == 1) {
                        $data['advertiser']['count'][$row['advertiser']]++;
                        $data['media']['count'][$row['media']]++;
                        $data['tab']['count'][$row['tab_name']]++;
                    }
                    if($row['advertiser'] && !@in_array($row['advertiser'], $data['advertiser']['list']))
					$data['advertiser']['list'][] = $row['advertiser']; //광고주 목록
                    if($row['media'] && !@in_array($row['media'], $data['advertiser']['media'][$row['advertiser']]))
                        $data['advertiser']['media'][$row['advertiser']][] = $row['media']; //광고주 내 매체 목록
                    if($row['tab_name'] && !@in_array($row['tab_name'], $data['advertiser']['tab'][$row['advertiser']]))
                        $data['advertiser']['tab'][$row['advertiser']][] = $row['tab_name']; //광고주 내 탭명 목록
                    
                    if($row['media'] && !@in_array($row['media'], $data['media']['list']))
                        $data['media']['list'][] = $row['media']; //매체 목록
                    if($row['advertiser'] && !@in_array($row['advertiser'], $data['media']['advertiser'][$row['media']]))
                        $data['media']['advertiser'][$row['media']][] = $row['advertiser']; //매체 내 광고주 목록
                    if($row['tab_name'] && !@in_array($row['tab_name'], $data['media']['tab'][$row['media']]))
                        $data['media']['tab'][$row['media']][] = $row['tab_name']; //매체 내 탭명 목록

                    if($row['tab_name'] && !@in_array($row['tab_name'], $data['tab']['list']))
                        $data['tab']['list'][] = $row['tab_name']; //탭명 목록
                    if($row['advertiser'] && !@in_array($row['advertiser'], $data['tab']['advertiser'][$row['tab_name']]))
                        $data['tab']['advertiser'][$row['tab_name']][] = $row['advertiser']; //탭명 내 광고주 목록
                    if($row['media'] && !@in_array($row['media'], $data['tab']['media'][$row['tab_name']]))
                        $data['tab']['media'][$row['tab_name']][] = $row['media']; // 탭명 내 매체 목록

                    switch($row['status']) {
                        case '99' : $data['stat']['확인']++; break;
                        case '12' : $data['stat']['본인아님']++; break;
                        case '11' : $data['stat']['미성년자']++; break;
                        case '10' : $data['stat']['업체불량']++; break;
                        case '9' : $data['stat']['지역불량']++; break;
                        case '8' : $data['stat']['이름불량']++; break;
                        case '7' : $data['stat']['테스트']++; break;
                        case '6' : $data['stat']['번호불량']++; break;
                        case '5' : $data['stat']['콜불량']++; break;
                        case '4' : $data['stat']['나이불량']++; break;
                        case '3' : $data['stat']['성별불량']++; break;
                        case '2' : $data['stat']['중복']++; break;
                        case '1' :
                        default : $data['stat']['인정']++; break;
                    }
                    $data['stat']['전체']++;

                    $data['data'][] = $row;
                }

                dd($data);
            } */

            /* $data = [
                'headers' => ['seq', 'event_seq', 'advertiser', 'media', 'device_width', 'lead', 'title', 'phone', 'age', 'gender', ''],
                'result' => $result['data'],
                'total_rows' => $result['total'],
                'page' => $param['page'],
                'limit' => $param['limit'],
                'sort_by' => $param['sort_by'],
                'sort_order' => $param['sort_order'],
            ]; */

            return $this->respond($result);
        //}else{
            return $this->fail("잘못된 요청");
        //}
    }
}
