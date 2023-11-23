<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\slack_api\SlackChat;
use App\Models\Api\UserModel;

use App\ThirdParty\googleads_api\ZenithGG;
use App\ThirdParty\jira_api\ZenithJira;
use CodeIgniter\CLI\CLI;
use Exception;

class JiraController extends BaseController
{
    use ResponseTrait;
    
    private $jira;

    public function __construct()
    {
        $this->jira = new ZenithJira(); 
    }

    public function getCode()
    {
        $result = $this->jira->getCode();
        dd($result);
    }

    public function callback()
    {
        $code = $this->request->getGet('code');
        if (empty($code)) {
            throw new \Exception('No authorization code provided');
        }
        $result = $this->jira->getToken($code);
        dd($result);
    }

    public function getProjects()
    {
        $result = $this->jira->getProjects();
        dd($result);
    }

    public function getIssues()
    {
        $result = $this->jira->getIssues();
    }

    public function getUsers()
    {
        $result = $this->jira->getIssues();
    }

    public function getIssueComplete()
    {  
        try {
            $this->writeLog($this->request, null, 'issue_complete_log');
            if (strtolower($this->request->getMethod()) === 'post') {
                $param = $this->request->getVar();
                if(!empty($param)){
                    $issueFields = $param->issue->fields ?? null;
                    $issueKey = $param->issue->key ?? '';
                    $actionUser = $param->user->displayName ?? '';
                    $reporterName = $issueFields->reporter->displayName ?? '';
                    $projectName = $issueFields->project->name ?? '';
                    $projectKey = $issueFields->project->key ?? '';
                    $issueSummary = $issueFields->summary ?? '';
                    
                    if(empty($reporterName)) return $this->fail("보고자 이름 오류");
                    $userModel = new UserModel();
                    $userData = $userModel->getUserByName($reporterName);
                    
                    if(empty($userData)) return $this->fail("일치하는 사용자 없음 ".$reporterName);
    
                    $slack = new SlackChat();
                    $issueLink = 'https://carelabs-dm.atlassian.net/jira/core/projects/' . $projectKey . '/board?selectedIssue=' . $issueKey;
                    
                    $slackMessage = [
                        'channel' => $slack->config['UserID'][$userData['nickname']],
                        'blocks' => [
                            [
                                'type' => 'section',
                                'text' => [
                                    'type' => 'mrkdwn',
                                    'text' => sprintf('[%s][%s] <%s|%s> %s님이 완료처리 하였습니다.', $projectName, $issueSummary, $issueLink, $issueKey, $actionUser),
                                ],
                                "block_id" => "text1"
                            ],
                        ],
                    ];
                    
                    $slackResult = $slack->sendMessage($slackMessage);
                }else{
                    throw new Exception("요청 데이터 오류");
                }
            }else{
                throw new Exception("요청 메소드 오류");
            }
        } catch (Exception $e) {
            $logText = "오류 메세지: ".$e->getMessage();
            $this->writeLog($this->request, $logText, 'issue_complete_log');
        }
    }

    public function writeLog($request = null, $addLog = null, $filename = null) 
    {
        if(is_null($request)){
            $request = $this->request;
        }

        if(is_null($filename)){
            $filename = 'jira_test_log';
        }

        $headers = $request->headers();
        array_walk($headers, function(&$value, $key) {
            $value = $value->getValue();
        });

        $logText = '---------------------------'."\n";
        $logText .= '요청 시간: '.date('Y-m-d H:i:s')."\n";
        $logText .= '요청 헤더: '.json_encode($headers, JSON_UNESCAPED_UNICODE)."\n";
        $logText .= '요청 메소드: '.$request->getMethod()."\n";
        $logText .= '요청 본문: '.json_encode($request->getBody(), JSON_UNESCAPED_UNICODE)."\n";
        if(!is_null($addLog)){
            $logText .= $addLog;
        }
        $fp = fopen(WRITEPATH.'/logs/'.$filename, 'a+');
        $fw = fwrite($fp, print_r($logText,true).PHP_EOL);
        fclose($fp);
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

    public function jsonFormatTest()
    {
        $data = "{\"issue\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/16328\",\"id\":16328,\"key\":\"VDO-1627\",\"changelog\":{\"startAt\":0,\"maxResults\":0,\"total\":0,\"histories\":null},\"fields\":{\"statuscategorychangedate\":\"2023-11-21T15:08:24.271+0900\",\"issuetype\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/issuetype\/10119\",\"id\":10119,\"description\":\"소규>모 개별 업무입니다.\",\"iconUrl\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/issuetype\/avatar\/10318?size=medium\",\"name\":\"작업\",\"untranslatedName\":null,\"subtask\":false,\"fields\":{},\"statuses\":[],\"namedValue\":\"작업\"},\"customfield_10190\":null,\"customfield_10193\":null,\"timespent\":null,\"customfield_10194\":null,\"customfield_10195\":null,\"customfield_10030\":null,\"project\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/project\/10114\",\"id\":10114,\"key\":\"VDO\",\"name\":\"영상요청판\",\"description\":null,\"avatarUrls\":{\"48x48\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/project\/avatar\/10400\",\"24x24\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/project\/avatar\/10400?size=small\",\"16x16\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/project\/avatar\/10400?size=xsmall\",\"32x32\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/project\/avatar\/10400?size=medium\"},\"issuetypes\":null,\"projectCategory\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/projectCategory\/10000\",\"id\":10000,\"name\":\"업무요청\",\"description\":\"\"},\"email\":null,\"lead\":null,\"components\":null,\"versions\":null,\"projectTypeKey\":\"business\",\"simplified\":false},\"customfield_10032\":null,\"fixVersions\":[],\"aggregatetimespent\":null,\"resolution\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/resolution\/10000\",\"id\":10000,\"name\":\"Done\",\"description\":\"\",\"namedValue\":\"Done\"},\"customfield_10027\":null,\"customfield_10028\":null,\"customfield_10029\":null,\"resolutiondate\":1700546904265,\"workratio\":-1,\"watches\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/issue\/VDO-1627\/watchers\",\"watchCount\":1,\"isWatching\":false},\"issuerestriction\":{\"issuerestrictions\":{},\"shouldDisplay\":false},\"lastViewed\":\"2023-11-21T12:07:25.250+0900\",\"customfield_10180\":null,\"customfield_10181\":null,\"customfield_10182\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/customFieldOption\/10204\",\"value\":\"4등급\",\"id\":\"10204\"},\"created\":1700533791897,\"customfield_10183\":null,\"customfield_10184\":null,\"customfield_10185\":null,\"customfield_10020\":null,\"customfield_10186\":null,\"customfield_10021\":null,\"customfield_10187\":null,\"customfield_10022\":null,\"customfield_10188\":null,\"customfield_10023\":null,\"priority\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/priority\/3\",\"id\":3,\"name\":\"보>통\",\"iconUrl\":\"https:\/\/carelabs-dm.atlassian.net\/images\/icons\/priorities\/medium.svg\",\"namedValue\":\"보통\"},\"customfield_10024\":null,\"customfield_10025\":\"10128_*:*_1_*:*_4895917_*|*_10132_*:*_1_*:*_0_*|*_10131_*:*_1_*:*_8216475\",\"labels\":[],\"customfield_10026\":null,\"customfield_10016\":null,\"customfield_10017\":null,\"customfield_10018\":{\"hasEpicLinkFieldDependency\":false,\"showField\":false,\"nonEditableReason\":{\"reason\":\"PLUGIN_LICENSE_ERROR\",\"message\":\"상위 링크는 Jira Premium 사용자만 이용할 수 있습니다.\"}},\"customfield_10019\":\"1|hzz8b3:000000000009\",\"aggregatetimeoriginalestimate\":null,\"timeestimate\":null,\"versions\":[],\"issuelinks\":[],\"assignee\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/user?accountId=712020%3A839e63de-b8c4-4313-afec-251e097905cc\",\"name\":null,\"key\":null,\"accountId\":\"712020:839e63de-b8c4-4313-afec-251e097905cc\",\"emailAddress\":null,\"avatarUrls\":{\"48x48\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-0.png\",\"24x24\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-0.png\",\"16x16\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-0.png\",\"32x32\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-0.png\"},\"displayName\":\"김예일\",\"active\":true,\"timeZone\":\"Asia\/Seoul\",\"groups\":null,\"locale\":null,\"accountType\":\"atlassian\"},\"updated\":1700546904615,\"status\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/status\/10132\",\"description\":null,\"iconUrl\":\"https:\/\/carelabs-dm.atlassian.net\/images\/icons\/status_generic.gif\",\"name\":\"Done\",\"untranslatedName\":null,\"id\":10132,\"statusCategory\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/statuscategory\/3\",\"id\":3,\"key\":\"done\",\"colorName\":\"green\",\"name\":\"Complete\"},\"untranslatedNameValue\":null},\"components\":[],\"customfield_10172\":null,\"timeoriginalestimate\":null,\"customfield_10173\":null,\"description\":\"기획서 참고\",\"customfield_10174\":null,\"customfield_10175\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/customFieldOption\/10199\",\"value\":\"1. 신규 영상 제작\",\"id\":\"10199\"},\"customfield_10010\":null,\"customfield_10176\":270000.0,\"customfield_10177\":15.0,\"customfield_10014\":null,\"customfield_10179\":null,\"timetracking\":{\"originalEstimate\":null,\"remainingEstimate\":null,\"timeSpent\":null,\"originalEstimateSeconds\":0,\"remainingEstimateSeconds\":0,\"timeSpentSeconds\":0},\"customfield_10015\":\"2023-11-21\",\"customfield_10005\":null,\"customfield_10006\":null,\"customfield_10007\":null,\"security\":null,\"aggregatetimeestimate\":null,\"attachment\":[],\"summary\":\"더행복한흉부외과의원 영상 제>작 요청\",\"creator\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/user?accountId=712020%3Ac0843a52-8296-49d0-911c-a5e13ab953ed\",\"name\":null,\"key\":null,\"accountId\":\"712020:c0843a52-8296-49d0-911c-a5e13ab953ed\",\"emailAddress\":null,\"avatarUrls\":{\"48x48\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-3.png\",\"24x24\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-3.png\",\"16x16\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-3.png\",\"32x32\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-3.png\"},\"displayName\":\"김진홍\",\"active\":true,\"timeZone\":\"Asia\/Seoul\",\"groups\":null,\"locale\":null,\"accountType\":\"atlassian\"},\"subtasks\":[],\"customfield_10163\":\"\\\\\\\\10.10.40.10\\\\바이브알씨\\\\디자인요청\\\\김진홍\\\\더행복한흉부외과의원\\\\231121_영상 제작\",\"reporter\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/user?accountId=712020%3Ac0843a52-8296-49d0-911c-a5e13ab953ed\",\"name\":null,\"key\":null,\"accountId\":\"712020:c0843a52-8296-49d0-911c-a5e13ab953ed\",\"emailAddress\":null,\"avatarUrls\":{\"48x48\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-3.png\",\"24x24\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-3.png\",\"16x16\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-3.png\",\"32x32\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-3.png\"},\"displayName\":\"김진홍\",\"active\":true,\"timeZone\":\"Asia\/Seoul\",\"groups\":null,\"locale\":null,\"accountType\":\"atlassian\"},\"customfield_10120\":[\"GDN\"],\"aggregateprogress\":{\"progress\":0,\"total\":0},\"customfield_10000\":\"{}\",\"customfield_10001\":null,\"customfield_10002\":null,\"customfield_10003\":null,\"customfield_10169\":null,\"customfield_10004\":null,\"environment\":null,\"customfield_10119\":[\"더행복한흉부외과의원\"],\"duedate\":null,\"progress\":{\"progress\":0,\"total\":0},\"votes\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/issue\/VDO-1627\/votes\",\"votes\":0,\"hasVoted\":false},\"comment\":{\"maxResults\":0,\"total\":0,\"startAt\":0,\"comments\":[],\"last\":false},\"worklog\":{\"maxResults\":20,\"total\":0,\"startAt\":0,\"worklogs\":[],\"last\":false}},\"renderedFields\":null},\"user\":{\"self\":null,\"name\":\"712020:839e63de-b8c4-4313-afec-251e097905cc\",\"key\":\"712020:839e63de-b8c4-4313-afec-251e097905cc\",\"accountId\":\"712020:839e63de-b8c4-4313-afec-251e097905cc\",\"emailAddress\":\"zfkltm320@carelabs.co.kr\",\"avatarUrls\":{\"48x48\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/712020:839e63de-b8c4-4313-afec-251e097905cc\/53ff948c-6d4b-4e72-a209-c26fb7acaa0d\/128\",\"24x24\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/712020:839e63de-b8c4-4313-afec-251e097905cc\/53ff948c-6d4b-4e72-a209-c26fb7acaa0d\/128\",\"16x16\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/712020:839e63de-b8c4-4313-afec-251e097905cc\/53ff948c-6d4b-4e72-a209-c26fb7acaa0d\/128\",\"32x32\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/712020:839e63de-b8c4-4313-afec-251e097905cc\/53ff948c-6d4b-4e72-a209-c26fb7acaa0d\/128\"},\"displayName\":\"김예일\",\"active\":true,\"timeZone\":null,\"groups\":null,\"locale\":\"ko\",\"accountType\":\"atlassian\"},\"timestamp\":1700546905833}";
        dd(json_decode($data));
    }
}
