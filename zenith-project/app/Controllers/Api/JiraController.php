<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\slack_api\SlackChat;
use App\Models\Api\UserModel;

use App\ThirdParty\googleads_api\ZenithGG;
use App\ThirdParty\jira_api\ZenithJira;
use CodeIgniter\CLI\CLI;

class JiraController extends BaseController
{
    use ResponseTrait;
    
    private $jira;

    public function __construct()
    {
        $this->jira = new ZenithJira(); 
    }

    public function getProjects()
    {
        $result = $this->jira->getProjects();
    }

    public function getIssues()
    {
        $result = $this->jira->getIssues();
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




    /* public function getAuthorizationCode()
    {
        $result = $this->jira->getAuthorizationCode();
        return redirect()->to($result);
    }

    public function oauth()
    {
        $code = 'eyJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI1MjM1ZmIyNC1kMTIwLTQ0ZTAtOTIxNC0yYTY3YTZlYzljYTIiLCJzdWIiOiI2M2Q5YmYyMzgyZWVlZTc4YTRjZGU5NTUiLCJuYmYiOjE2OTMxODU1NzksImlzcyI6ImF1dGguYXRsYXNzaWFuLmNvbSIsImlhdCI6MTY5MzE4NTU3OSwiZXhwIjoxNjkzMTg1ODc5LCJhdWQiOiJYQ21BRUZNRkFPMzFqVXJkVEZScGl4MmhGMEkwWkJ4eSIsImNsaWVudF9hdXRoX3R5cGUiOiJQT1NUIiwiaHR0cHM6Ly9pZC5hdGxhc3NpYW4uY29tL3ZlcmlmaWVkIjp0cnVlLCJodHRwczovL2lkLmF0bGFzc2lhbi5jb20vdWp0IjoiNTIzNWZiMjQtZDEyMC00NGUwLTkyMTQtMmE2N2E2ZWM5Y2EyIiwic2NvcGUiOlsicmVhZDpqaXJhLXdvcmsiXSwiaHR0cHM6Ly9pZC5hdGxhc3NpYW4uY29tL2F0bF90b2tlbl90eXBlIjoiQVVUSF9DT0RFIiwiaHR0cHM6Ly9pZC5hdGxhc3NpYW4uY29tL2hhc1JlZGlyZWN0VXJpIjp0cnVlLCJodHRwczovL2lkLmF0bGFzc2lhbi5jb20vc2Vzc2lvbl9pZCI6IjRhZjU3ZTk1LTZmNjAtNDg1My1iODYwLTViZDU4Y2Y3ODBhMSIsImh0dHBzOi8vaWQuYXRsYXNzaWFuLmNvbS9wcm9jZXNzUmVnaW9uIjoidXMtZWFzdC0xIn0.3TjwBJl1l5i1YoM1vQO-eVsL2ERlXlLLGiX5hQCU5wI';
        $result = $this->jira->getAccessToken($code);
        dd($result);
    } */
}
