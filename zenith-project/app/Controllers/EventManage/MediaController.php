<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;
use App\Models\EventManage\MediaModel;
use CodeIgniter\API\ResponseTrait;

class MediaController extends BaseController
{
    use ResponseTrait;
    
    protected $media;
    public function __construct() 
    {
        $this->media = model(MediaModel::class);
    }
    
    public function index()
    {
        return view('/events/media/media');
    }

    public function getList()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->media->getMedias($arg);
            $list = $result['data'];
            
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

    public function getMedia()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'get'){
            $arg = $this->request->getGet();
            $result = $this->media->getMedia($arg);
            
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function createMedia()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'post'){
            $arg = $this->request->getRawInput();
            $data = [
                'media' => $arg['media'],
				'target' => $arg['target'],
            ];

            $validation = \Config\Services::validation();
            $validationRules      = [
                'media' => 'required|is_unique[event_media.media]',
            ];
            $validationMessages   = [
                'media' => [
                    'required' => '매체명은 필수 입력 사항입니다.',
                    'is_unique' => '이미 등록된 매체입니다.'
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }
            
            $result = $this->media->createMedia($data);
            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function updateMedia()
    {
        if($this->request->isAJAX() && strtolower($this->request->getMethod()) === 'put'){
            $arg = $this->request->getRawInput();
            $data = [
                'media' => $arg['media'],
				'target' => $arg['target'],
            ];

            $validation = \Config\Services::validation();
            $validationRules      = [
                'media' => 'required',
            ];
            $validationMessages   = [
                'media' => [
                    'required' => '매체명은 필수 입력 사항입니다.',
                ],
            ];
            $validation->setRules($validationRules, $validationMessages);
            if (!$validation->run($data)) {
                $errors = $validation->getErrors();
                return $this->failValidationErrors($errors);
            }

            $result = $this->media->updateMedia($data, $arg['seq']);

            return $this->respond($result);
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
