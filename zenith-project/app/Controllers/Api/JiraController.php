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
                    $issueLink = 'https://carelabs-dm.atlassian.net/jira/core/projects/DEV/board?selectedIssue=' . $issueKey;
                    $text = sprintf('[개발요청판]<%s|[%s:%s]> "요청 준비중" 상태로 %s 경과하였습니다.'.PHP_EOL.'해당 이슈를 확인하신 후 자료가 준비되었으면 "요청" 상태로 변경해주십시오.', $issueLink, $issueKey, $subject, $passTime);
                    
                    $this->sendSlackMessage($creator, $text);
                }
            }
        }
    }

    public function getIssueAction()
    {  
        try {
            $this->writeLog($this->request, null, 'issue_complete_log');
            if (strtolower($this->request->getMethod()) === 'post') {
                $param = $this->request->getVar();
                       
                $changeItems = $param->changelog->items;
                $issueFields = $param->issue->fields ?? null;
                $issueKey = $param->issue->key ?? '';
                $actionUser = $param->user->displayName ?? '';
                $reporterName = $issueFields->reporter->displayName ?? null;
                $designerName = $issueFields->customfield_10179[0]->displayName ?? null;
                $projectName = $issueFields->project->name ?? '';
                $projectKey = $issueFields->project->key ?? '';
                $issueSummary = $issueFields->summary ?? '';
                
                foreach ($changeItems as $item) {
                    $changeField = $item->field;
                    $changeStatus = $item->to;

                    if($projectKey == 'DEV' && $changeField == 'status'){
                        $issueLink = 'https://carelabs-dm.atlassian.net/jira/core/projects/' . $projectKey . '/board?selectedIssue=' . $issueKey;
                        if($changeStatus == '1'){//요청
                            
                        }else if($changeStatus == '10131'){//진행중
                            $sendText = sprintf('[%s][%s] <%s|%s> %s님이 요청하신 작업을 시작합니다.', $projectName, $issueSummary, $issueLink, $issueKey, $actionUser);
                            $this->sendSlackMessage($reporterName, $sendText);
                            if(!empty($designerName) && $reporterName != $designerName){
                                $this->sendSlackMessage($designerName, $sendText);
                            }
                        }else if($changeStatus == '10136'){//검수중
                            $sendText = sprintf('[%s][%s] <%s|%s> %s님이 작업이 완료되어 %s님께 최종 검수를 요청하였습니다.'.PHP_EOL.'링크로 이동하셔서 *댓글로 확인 여부 및 피드백* 남겨주세요.(*보고자 확인 후 완료로 처리*됩니다.)', $projectName, $issueSummary, $issueLink, $issueKey, $actionUser, $reporterName);
                            $this->sendSlackMessage($reporterName, $sendText);

                            if(!empty($designerName) && $reporterName != $designerName){
                                $sendText = sprintf('[%s][%s] <%s|%s> %s님이 작업이 완료되어 %s님께 디자인 검수를 요청하였습니다.'.PHP_EOL.'링크로 이동하셔서 *댓글로 확인 여부 및 피드백* 남겨주세요.', $projectName, $issueSummary, $issueLink, $issueKey, $actionUser, $designerName);
                                $this->sendSlackMessage($designerName, $sendText);
                            }
                        }else if($changeStatus == '10132'){//완료됨
                            $sendText = sprintf('[%s][%s] <%s|%s> 요청하신 작업이 검수가 완료되어 해당 이슈가 종료되었습니다.', $projectName, $issueSummary, $issueLink, $issueKey, $actionUser);
                            $this->sendSlackMessage($reporterName, $sendText);
                        }
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

    public function sendSlackMessage($username, $text)
    {
        $userModel = new UserModel();
        $userData = $userModel->getUserByName($username);

        $slack = new SlackChat();
        
        $slackMessage = [
            'channel' => $slack->config['UserID'][$userData['nickname']],
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => $text,
                    ],
                    "block_id" => "text1"
                ],
            ],
        ];
        
        $slack->sendMessage($slackMessage);
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
        $data = 
        '{"statuscategorychangedate":"2023-12-18T17:20:45.889+0900","issuetype":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/issuetype\/10119","id":"10119","description":"\uc18c\uaddc\ubaa8 \uac1c\ubcc4 \uc5c5\ubb34\uc785\ub2c8\ub2e4.","iconUrl":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/issuetype\/avatar\/10318?size=medium","name":"\uc791\uc5c5","subtask":false,"avatarId":10318,"hierarchyLevel":0},"customfield_10190":null,"timespent":null,"customfield_10193":null,"customfield_10194":null,"customfield_10195":null,"customfield_10030":null,"project":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/project\/10114","id":"10114","key":"VDO","name":"\uc601\uc0c1\uc694\uccad\ud310","projectTypeKey":"business","simplified":false,"avatarUrls":{"48x48":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/project\/avatar\/10400","24x24":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/project\/avatar\/10400?size=small","16x16":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/project\/avatar\/10400?size=xsmall","32x32":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/universal_avatar\/view\/type\/project\/avatar\/10400?size=medium"},"projectCategory":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/projectCategory\/10000","id":"10000","description":"","name":"\uc5c5\ubb34\uc694\uccad"}},"customfield_10032":null,"fixVersions":[],"aggregatetimespent":null,"resolution":null,"customfield_10027":null,"customfield_10028":null,"customfield_10029":null,"resolutiondate":null,"workratio":-1,"lastViewed":"2023-12-18T17:21:30.671+0900","issuerestriction":{"issuerestrictions":[],"shouldDisplay":false},"watches":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/issue\/VDO-1986\/watchers","watchCount":1,"isWatching":false},"customfield_10180":null,"customfield_10181":null,"created":"2023-12-18T17:20:45.489+0900","customfield_10182":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/customFieldOption\/10203","value":"5\ub4f1\uae09","id":"10203"},"customfield_10183":null,"customfield_10184":null,"customfield_10020":null,"customfield_10185":null,"customfield_10186":null,"customfield_10021":null,"customfield_10187":null,"customfield_10022":null,"customfield_10188":null,"priority":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/priority\/3","iconUrl":"https:\/\/carelabs-dm.atlassian.net\/images\/icons\/priorities\/medium.svg","name":"\ubcf4\ud1b5","id":"3"},"customfield_10023":null,"customfield_10024":null,"customfield_10025":null,"customfield_10026":null,"labels":[],"customfield_10016":null,"customfield_10017":null,"customfield_10018":{"hasEpicLinkFieldDependency":false,"showField":false,"nonEditableReason":{"reason":"PLUGIN_LICENSE_ERROR","message":"\uc0c1\uc704 \ub9c1\ud06c\ub294 Jira Premium \uc0ac\uc6a9\uc790\ub9cc \uc774\uc6a9\ud560 \uc218 \uc788\uc2b5\ub2c8\ub2e4."}},"customfield_10019":"1|i00hgr:","timeestimate":null,"aggregatetimeoriginalestimate":null,"versions":[],"issuelinks":[],"assignee":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/user?accountId=712020%3Aa897c4ef-6626-4f23-a44c-d6c3442a6e04","accountId":"712020:a897c4ef-6626-4f23-a44c-d6c3442a6e04","avatarUrls":{"48x48":"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-3.png","24x24":"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-3.png","16x16":"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-3.png","32x32":"https:\/\/avatar-management--avatars.us-west-2.prod.public.atl-paas.net\/default-avatar-3.png"},"displayName":"\uc774\ud6a8\uc6d0","active":true,"timeZone":"Asia\/Seoul","accountType":"atlassian"},"updated":"2023-12-18T17:21:31.105+0900","status":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/status\/10137","iconUrl":"https:\/\/carelabs-dm.atlassian.net\/images\/icons\/status_generic.gif","name":"\ud574\uc57c \ud560 \uc77c","id":"10137","statusCategory":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/statuscategory\/2","id":2,"key":"new","colorName":"blue-gray","name":"New"}},"components":[],"customfield_10172":null,"timeoriginalestimate":null,"customfield_10173":null,"description":"\ud50c\ub780\uce58\uacfc\uc758\uc6d0 \uc601\uc0c1 \uc218\uc815 \uc694\uccad\ub4dc\ub9bd\ub2c8\ub2e4.\n\n\uac10\uc0ac\ud569\ub2c8\ub2e4.","customfield_10174":null,"customfield_10010":null,"customfield_10175":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/customFieldOption\/10200","value":"2. \uc601\uc0c1 \uc218\uc815","id":"10200"},"customfield_10176":30000,"customfield_10177":1,"customfield_10014":null,"customfield_10179":null,"timetracking":[],"customfield_10015":null,"customfield_10005":null,"customfield_10006":null,"customfield_10007":null,"security":null,"attachment":[],"aggregatetimeestimate":null,"summary":"\ud50c\ub780\uce58\uacfc\uc758\uc6d0 \ub79c\ub529 \uc601\uc0c1 \uc218\uc815 \uc694\uccad","creator":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/user?accountId=712020%3A401972cb-14b1-47fd-ae34-842cf03fbc52","accountId":"712020:401972cb-14b1-47fd-ae34-842cf03fbc52","avatarUrls":{"48x48":"https:\/\/secure.gravatar.com\/avatar\/0b8d06ca8c8aeebaf49dd11a5f152e29?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-2.png","24x24":"https:\/\/secure.gravatar.com\/avatar\/0b8d06ca8c8aeebaf49dd11a5f152e29?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-2.png","16x16":"https:\/\/secure.gravatar.com\/avatar\/0b8d06ca8c8aeebaf49dd11a5f152e29?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-2.png","32x32":"https:\/\/secure.gravatar.com\/avatar\/0b8d06ca8c8aeebaf49dd11a5f152e29?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-2.png"},"displayName":"\uc815\ucc2c\uc601","active":true,"timeZone":"Asia\/Seoul","accountType":"atlassian"},"subtasks":[],"customfield_10163":"\\\\10.10.40.10\\\ubc14\uc774\ube0c\uc54c\uc528\\\ub514\uc790\uc778\uc694\uccad\\\uc815\ucc2c\uc601\\\ud50c\ub780\uce58\uacfc\\231218 \ub79c\ub529 \uc601\uc0c1 \uc218\uc815 \uc694\uccad","customfield_10120":["GDN"],"reporter":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/user?accountId=712020%3A401972cb-14b1-47fd-ae34-842cf03fbc52","accountId":"712020:401972cb-14b1-47fd-ae34-842cf03fbc52","avatarUrls":{"48x48":"https:\/\/secure.gravatar.com\/avatar\/0b8d06ca8c8aeebaf49dd11a5f152e29?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-2.png","24x24":"https:\/\/secure.gravatar.com\/avatar\/0b8d06ca8c8aeebaf49dd11a5f152e29?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-2.png","16x16":"https:\/\/secure.gravatar.com\/avatar\/0b8d06ca8c8aeebaf49dd11a5f152e29?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-2.png","32x32":"https:\/\/secure.gravatar.com\/avatar\/0b8d06ca8c8aeebaf49dd11a5f152e29?d=https%3A%2F%2Favatar-management--avatars.us-west-2.prod.public.atl-paas.net%2Fdefault-avatar-2.png"},"displayName":"\uc815\ucc2c\uc601","active":true,"timeZone":"Asia\/Seoul","accountType":"atlassian"},"customfield_10000":"{}","aggregateprogress":{"progress":0,"total":0},"customfield_10001":null,"customfield_10002":null,"customfield_10003":null,"customfield_10004":null,"customfield_10169":null,"environment":null,"customfield_10119":["\ud50c\ub780\uce58\uacfc\uc758\uc6d0"],"duedate":null,"progress":{"progress":0,"total":0},"votes":{"self":"https:\/\/carelabs-dm.atlassian.net\/rest\/api\/2\/issue\/VDO-1986\/votes","votes":0,"hasVoted":false}}';
        dd(json_decode(preg_replace('/[\x00-\x1F\x7F]/u', '', $data), true));
    }
}
