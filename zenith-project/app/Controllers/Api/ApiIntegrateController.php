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
            
            $param['sdate'] = '2023-04-03';
            $param['edate'] = '2023-04-04';

            $results = $this->integrate->getEventLead($param);
            $total = $results['allCount'];

            $result = [
                'data' => $results['data'],
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'draw' => intval($param['draw']),
            ];

            return $this->respond($result);
        //}else{
            return $this->fail("잘못된 요청");
        //}
    }

    public function getAdvertiser()
    {
        $param = $this->request->getGet();
        $param['sdate'] = '2023-04-03';
        $param['edate'] = '2023-04-04';
        $result = $this->integrate->getAdvertiser($param);

        return $this->respond($result);
    }

    public function getMedia()
    {
        $param = $this->request->getGet();
        $param['sdate'] = '2023-04-03';
        $param['edate'] = '2023-04-04';
        $result = $this->integrate->getMedia($param);

        return $this->respond($result);
    }

    public function getEvent()
    {
        $param = $this->request->getGet();
        $param['sdate'] = '2023-04-03';
        $param['edate'] = '2023-04-04';
        $result = $this->integrate->getEvent($param);

        return $this->respond($result);
    }
}
