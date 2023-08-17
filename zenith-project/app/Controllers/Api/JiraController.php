<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\slack_api\SlackChat;
use App\Models\Api\UserModel;
use App\ThirdParty\facebook_api\ZenithFB;
use App\ThirdParty\jira_api\ZenithJira;
use CodeIgniter\CLI\CLI;

class JiraController extends BaseController
{
    use ResponseTrait;
    
    private $jira, $zenith;

    public function __construct()
    {
        $this->jira = new ZenithJira(); 
    }

    public function getIssueComplete()
    {  
        if (strtolower($this->request->getMethod()) === 'post') {
            $param = $this->request->getVar();
            log_message('info', print_r($param, true));
            if(!empty($param)){
                $reporter = $param->issue->fields->reporter->displayName ?? '';
                $projectName = $param->issue->fields->project->name ?? '';
                $projectType = $param->issue->fields->project->key ?? '';
                $projectSummary = $param->issue->fields->summary ?? '';
                $issueKey = $param->issue->key ?? '';
                $actionUser = $param->user->displayName ?? '';
                $user = new UserModel();
                $userData = $user->getUserByName($reporter);
                $slack = new SlackChat();
                $link = 'https://carelabs-dm.atlassian.net/jira/core/projects/'.$projectType.'/board?selectedIssue='.$issueKey.'';
                $data = [
                    'channel' => $slack->config['UserID'][$userData['nickname']],
                    'blocks' => [
                        [
                            'type' => 'section',
                            'text' => [
                                'type' => 'mrkdwn',
                                'text' => '['.$projectName.']['.$projectSummary.'] <'.$link.'|'.$issueKey.'> '.$actionUser.'님이 완료처리 하였습니다.',
                            ],
                            "block_id" => "text1"
                        ],
                    ],
                ];
                $result = $slack->sendMessage($data);
                log_message('info', print_r($result, true));
            }
        }else{
            return $this->fail("잘못된 요청");
        }
    }

    public function test()
    {
        $this->zenith = new ZenithFB();  
        CLI::write("시작", "light_red");
        $from = '2023-08-11';
        $to = '2023-08-11';
        $this->zenith->getAdLead($from, $to);
    }
}
