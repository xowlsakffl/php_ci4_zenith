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
        //if($this->request->isAJAX()){
            $param = $this->request->getPost();
            $results = $this->integrate->getEventLead()->paginate(10);
            $data = [
                //'advertiser' => $this->integrate->getAdvertiser($param),
                'headers' => ['seq', 'event_seq', '', '', '', 'name', 'phone', 'age', 'gender', ''],
                'result' => $results,
                //'total_rows' => $builder->pager->getTotal(),
            ];
    
            return $this->respond($data);
        //}else{

        //}
    }
}
