<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\slack_api\SlackChat;
use App\Models\Api\UserModel;
use App\Models\EventManage\EventModel;
use App\ThirdParty\googleads_api\ZenithGG;
use App\ThirdParty\jira_api\ZenithJira;
use CodeIgniter\CLI\CLI;
use DateTime;
use Exception;

class JiraController extends BaseController
{
    use ResponseTrait;
    
    private $jira, $event;

    public function __construct()
    {
        $this->jira = new ZenithJira(); 
        $this->event = model(EventModel::class);
    }

    /* public function getCode()
    {
        $url = $this->jira->getCode();
        return redirect()->to($url);
    }

    public function callback()
    {
        $code = $this->request->getGet('code');
        if (empty($code)) {
            throw new \Exception('No authorization code provided');
        }
        $result = $this->jira->getToken($code);
        dd($result);
    } */

    public function testRequest()
    {
        $result = $this->jira->testRequest();
        dd($result);
    }

    public function getProjects()
    {
        $result = $this->jira->getProjects();
        dd($result);
    }

    public function getIssue()
    {
        $result = $this->jira->getIssue();
        dd($result);
    }

    public function getIssueEventData()
    {
        $events = $this->event->getInformationAll();
        $developIssues = $this->jira->getDevelopIssues("Done");
        if(!empty($events)){
            foreach ($developIssues as $issue) {
                $type = $issue->fields->customfield_10172->value ?? null;
                if(isset($type) && $type == '1.1 신규 랜딩제작'){
                    $designer = $issue->fields->customfield_10179[0]->displayName ?? null;
                    $developer = $issue->fields->assignee->displayName ?? null;
                    $eventUrls = $issue->fields->customfield_10188 ?? null;
                    if(!empty($eventUrls) && (!empty($designer) || !empty($developer))){
                        preg_match_all('/\/(\d+)\b/', $eventUrls, $matches);
                        foreach ($matches[1] as $eventSeq) {
                            $data = [
                                'designer' => $designer,
                                'developer' => $developer
                            ];
                            foreach ($events as $event) {
                                if($eventSeq == $event['seq']){
                                    $result = $this->event->updateEventWorker($data, $event['seq']);
                                    
                                    if(isset($result)){print_r($event['seq']." 완료".PHP_EOL);}
                                }
                            }
                        }
                    }
                }
                
            }
        }
    }

    public function getIssuePreparing()
    {
        $developIssues = $this->jira->getDevelopIssues("요청 준비 중");
        if(!empty($developIssues)){
            foreach ($developIssues as $issue) {
                $created = $issue->fields->created;
                $subject = $issue->fields->summary;
                
                $now = new DateTime();
                $interval = $now->diff($created);
                $passHour = $interval->h;
                $passMinute = $interval->i;
                $passTime = $passHour."시간 ".$passMinute."분";
                if ($interval->h >= 1) {
                    $creator = $issue->fields->creator->displayName;
                    $issueKey = $issue->key ?? null;
                    $userModel = new UserModel();
                    $userData = $userModel->getUserByName($creator);

                    $slack = new SlackChat();
                    $issueLink = 'https://carelabs-dm.atlassian.net/jira/core/projects/DEV/board?selectedIssue=' . $issueKey;
                    $slackMessage = [
                        'channel' => $slack->config['UserID'][$userData['nickname']],
                        'blocks' => [
                            [
                                'type' => 'section',
                                'text' => [
                                    'type' => 'mrkdwn',
                                    'text' => sprintf('[개발요청판]<%s|[%s:%s]> "요청 준비중" 상태로 %s 경과하였습니다.'.PHP_EOL.'해당 이슈를 확인하신 후 자료가 준비되었으면 "요청" 상태로 변경해주십시오.', $issueLink, $issueKey, $subject, $passTime),
                                ],
                                "block_id" => "text1"
                            ],
                        ],
                    ];
                    
                    $slackResult = $slack->sendMessage($slackMessage);
                }
            }
        }
    }

    public function getIssueComplete()
    {  
        try {
            if (strtolower($this->request->getMethod()) === 'post') {
                $this->writeLog($this->request, null, 'jira_test_log');
                $param = $this->request->getVar();
                $this->writeLog($this->request, "디버깅:".json_encode($param), 'jira_test_log');
                $changeItems = $param->changelog->items;
                $issueFields = $param->issue->fields ?? null;
                $issueKey = $param->issue->key ?? '';
                $actionUser = $param->user->displayName ?? '';
                $reporterName = $issueFields->reporter->displayName ?? null;
                $projectName = $issueFields->project->name ?? '';
                $projectKey = $issueFields->project->key ?? '';
                $issueSummary = $issueFields->summary ?? '';

                foreach ($changeItems as $item) {
                    $changeField = $item->field;
                    $changeStatus = $item->to;
                    if($changeField == 'status' && $changeStatus == '10132'){
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
                        
                        $slack->sendMessage($slackMessage);
                        break;
                    }
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

    public function jsonFormatTest()
    {
        $data = "{\"timestamp\":1702879050864,\"webhookEvent\":\"jira:issue_updated\",\"issue_event_type_name\":\"issue_generic\",\"user\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/user?accountId=63d9bf2382eeee78a4cde955\",\"accountId\":\"63d9bf2382eeee78a4cde955\",\"avatarUrls\":{\"48x48\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\",\"24x24\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\",\"16x16\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\",\"32x32\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\"},\"displayName\":\"안민성\",\"active\":true,\"timeZone\":\"Asia\/Seoul\",\"accountType\":\"atlassian\"},\"issue\":{\"id\":\"17158\",\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/17158\",\"key\":\"DEV-1173\",\"fields\":{\"statuscategorychangedate\":\"2023-12-18T14:57:30.836+0900\",\"issuetype\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/issuetype\/10131\",\"id\":\"10131\",\"description\":\"랜딩 개발\/수정\",\"iconUrl\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/issuetype\/avatar\/10309?size=medium\",\"name\":\"랜딩\",\"subtask\":false,\"avatarId\":10309,\"hierarchyLevel\":0},\"customfield_10190\":null,\"timespent\":null,\"customfield_10193\":null,\"customfield_10194\":null,\"customfield_10030\":null,\"customfield_10195\":null,\"project\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/project\/10115\",\"id\":\"10115\",\"key\":\"DEV\",\"name\":\"개발요청판
            \",\"projectTypeKey\":\"business\",\"simplified\":false,\"avatarUrls\":{\"48x48\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/project\/avatar\/10422\",\"24x24\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/project\/avatar\/10422?size=small\",\"16x16\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/project\/avatar\/10422?size=xsmall\",\"32x32\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/project\/avatar\/10422?size=medium\"},\"projectCategory\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/projectCategory\/10000\",\"id\":\"10000\",\"description\":\"\",\"name\":\"업무요청\"}},\"customfield_10032\":null,\"fixVersions\":[],\"aggregatetimespent\":null,\"resolution\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/resolution\/10000\",\"id\":\"10000\",\"description\":\"\",\"name\":\"Done\"},\"customfield_10027\":null,\"customfield_10028\":null,\"customfield_10029\":null,\"resolutiondate\":\"2023-12-18T14:57:30.832+0900\",\"workratio\":-1,\"issuerestriction\":{\"issuerestrictions\":{},\"shouldDisplay\":false},\"lastViewed\":null,\"watches\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/issue\/DEV-1173\/watchers\",\"watchCount\":1,\"isWatching\":false},\"customfield_10180\":null,\"customfield_10181\":null,\"customfield_10182\":null,\"created\":\"2023-12-05T12:48:26.013+0900\",\"customfield_10183\":null,\"customfield_10184\":null,\"customfield_10020\":null,\"customfield_10185\":null,\"customfield_10186\":null,\"customfield_10021\":null,\"customfield_10022\":null,\"customfield_10187\":null,\"customfield_10023\":null,\"customfield_10188\":null,\"priority\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/priority\/3\",\"iconUrl\":\"https:\/\/carelabs-dm.atlassian.net\/images\/icons\/priorities\/medium.svg\",\"name\":\"보통\",\"id\":\"3\"},\"customfield_10024\":null,\"customfield_10025\":\"10128_*:*_1_*:*_503232_*|*_1_*:*_3_*:*_36041_*|*_10132_*:*_3_*:*_1130242879_*|*_10131_*:*_3_*:*_25000_*|*_10136_*:*_3_*:*_137699\",\"customfield_10026\":null,\"labels\":[],\"customfield_10016\":null,\"customfield_10017\":null,\"customfield_10018\":{\"hasEpicLinkFieldDependency\":false,\"showField\":false,\"nonEditableReason\":{\"reason\":\"PLUGIN_LICENSE_ERROR\",\"message\":\"상위 링>크는 Jira Premium 사용자만 이용할 수 있습니다.\"}},\"customfield_10019\":\"1|i00c9l:\",\"aggregatetimeoriginalestimate\":null,\"timeestimate\":null,\"versions\":[],\"issuelinks\":[],\"assignee\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/user?accountId=63d9bf2382eeee78a4cde955\",\"accountId\":\"63d9bf2382eeee78a4cde955\",\"avatarUrls\":{\"48x48\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\",\"24x24\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\",\"16x16\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\",\"32x32\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\"},\"displayName\":\"안민성\",\"active\":true,\"timeZone\":\"Asia\/Seoul\",\"accountType\":\"atlassian\"},\"updated\":\"2023-12-18T14:57:30.836+0900\",\"status\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/status\/10132\",\"iconUrl\":\"https:\/\/carelabs-dm.atlassian.net\/images\/icons\/status_generic.gif\",\"name\":\"Done\",\"id\":\"10132\",\"statusCategory\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/statuscategory\/3\",\"id\":3,\"key\":\"done\",\"colorName\":\"green\",\"name\":\"Complete\"}},\"components\":[],\"timeoriginalestimate\":null,\"customfield_10172\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/customFieldOption\/10172\",\"value\":\"1.2 랜딩 수정\",\"id\":\"10172\"},\"customfield_10173\":null,\"customfield_10174\":null,\"description\":\"테스트\",\"customfield_10010\":null,\"customfield_10175\":null,\"customfield_10176\":null,\"customfield_10177\":null,\"customfield_10014\":null,\"customfield_10179\":null,\"timetracking\":{},\"customfield_10015\":\"2023-12-18\",\"customfield_10005\":null,\"customfield_10006\":null,\"security\":null,\"customfield_10007\":null,\"aggregatetimeestimate\":null,\"attachment\":[],\"summary\":\"테스트\",\"creator\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/user?accountId=63d9bf23c2b1cb6b346fe898\",\"accountId\":\"63d9bf23c2b1cb6b346fe898\",\"avatarUrls\":{\"48x48\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\",\"24x24\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\",\"16x16\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\",\"32x32\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\"},\"displayName\":\"서지희\",\"active\":true,\"timeZone\":\"Asia\/Seoul\",\"accountType\":\"atlassian\"},\"subtasks\":[],\"customfield_10163\":null,\"reporter\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/user?accountId=63d9bf23c2b1cb6b346fe898\",\"accountId\":\"63d9bf23c2b1cb6b346fe898\",\"avatarUrls\":{\"48x48\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\",\"24x24\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\",\"16x16\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\",\"32x32\":\"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-6.png\"},\"displayName\":\"서지희\",\"active\":true,\"timeZone\":\"Asia\/Seoul\",\"accountType\":\"atlassian\"},\"customfield_10120\":[\"기타\"],\"customfield_10000\":\"{}\",\"aggregateprogress\":{\"progress\":0,\"total\":0},\"customfield_10001\":null,\"customfield_10002\":null,\"customfield_10003\":null,\"customfield_10004\":null,\"customfield_10169\":null,\"environment\":null,\"customfield_10119\":[\"플란치과병원부산점\"],\"duedate\":\"2023-12-18\",\"progress\":{\"progress\":0,\"total\":0},\"votes\":{\"self\":\"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/issue\/DEV-1173\/votes\",\"votes\":0,\"hasVoted\":false}}},\"changelog\":{\"id\":\"55981\",\"items\":[{\"field\":\"resolution\",\"fieldtype\":\"jira\",\"fieldId\":\"resolution\",\"from\":null,\"fromString\":null,\"to\":\"10000\",\"toString\":\"Done\"},{\"field\":\"status\",\"fieldtype\":\"jira\",\"fieldId\":\"status\",\"from\":\"10136\",\"fromString\":\"검수 중\",\"to\":\"10132\",\"toString\":\"Done\"}]}}";
        dd(json_decode(preg_replace('/[\x00-\x1F\x7F]/u', '', $data), true));
    }
}
