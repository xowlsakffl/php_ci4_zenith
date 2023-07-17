<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\slack_api\SlackChat;
use App\Models\Api\UserModel;

class JiraController extends BaseController
{
    use ResponseTrait;
    
    public function getIssueComplete()
    {  
        if (strtolower($this->request->getMethod()) === 'post') {
            $param = $this->request->getVar();
            if($param){
                $user = new UserModel();
                $userData = $user->getUserByEmail($param->user->emailAddress);
                $slack = new SlackChat();
                $data = [
                    'channel' => $slack->config['UserID'][$userData['nickname']],
                    'text' => '['.$param->issue->fields->project->name.']['.$param->issue->fields->summary.'('.$param->issue->key.')]'.$userData['nickname'].'님이 완료처리 하였습니다.'
                ];
                $result = $slack->sendMessage($data);
                log_message('info', print_r($result, true));
            }
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
