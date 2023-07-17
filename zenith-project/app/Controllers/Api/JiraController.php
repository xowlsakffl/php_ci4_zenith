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
                $userData = $user->getUserByName($param->issue->fields->reporter->displayName);
                $slack = new SlackChat();
                $data = [
                    'channel' => $slack->config['UserID'][$userData['nickname']],
                    'text' => '['.$param->issue->fields->project->name.']['.$param->issue->fields->summary.'('.$param->issue->key.')]'.$param->user->displayName.'님이 완료처리 하였습니다.<br><a href="https://carelabs-dm.atlassian.net/jira/core/projects/DEV/board?selectedIssue=DEV-18"></a>',
                ];
                $result = $slack->sendMessage($data);
                log_message('info', print_r($result, true));
            }
        }else{
            return $this->fail("잘못된 요청");
        }
    }
}
