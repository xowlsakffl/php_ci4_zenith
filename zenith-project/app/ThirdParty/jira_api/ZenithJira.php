<?php
namespace App\ThirdParty\jira_api;

require_once __DIR__ . '/vendor/autoload.php';

use Exception;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;
use JiraRestApi\User\UserService;

class ZenithJira
{
    //jira api 패키지
    private $iss;
    private $jiraHost = "https://carelabs-dm.atlassian.net";

    private $clientId, $clientSecret, $callbackUrl, $scopes, $accessToken, $refreshToken, $db;

    public function __construct()
    {
        $this->clientId = 'iiisb2fVWfvBNxfazgJhsPxDkgGKldQy';
        $this->clientSecret = 'ATOAEa7qX_LpMrJnt-rj3SQsQcGU4Ejg6skpynqWqnOoUnihyvAOi4C_Ur_h-1HahQFp08E74721';
        $this->callbackUrl = 'https://carezenith.co.kr/jira/callback'; 
        $this->scopes = 'manage:jira-project write:jira-work read:jira-work offline_access';

        $this->db = new JIRADB();
        try {
            $token = $this->db->getToken();
            if ($token['access_token']) {
                $this->accessToken = $token['access_token'];
                $this->refreshToken = $token['refresh_token'];
                if (time() >= strtotime($token['expires_time'] . ' -30 minute')){
                    $this->getRefreshToken();
                }
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }

        $this->iss = new ArrayConfiguration(
            [
                 'jiraHost' => $this->jiraHost,
                 'useTokenBasedAuth' => false,
                 'personalAccessToken' => $this->accessToken,
                  
                  // custom log config
                 'jiraLogEnabled' => true,
                 'jiraLogFile' => "my-jira-rest-client.log",
                 'jiraLogLevel' => 'INFO',
            ]
        );
    }

    public function getCode()
    {
        $params = [
            'audience' => 'api.atlassian.com',
            'client_id' => $this->clientId,
            'scope' => $this->scopes,
            'redirect_uri' => $this->callbackUrl,
            'response_type' => 'code',
            'state' => 'random-state',
        ];

        $url = 'https://auth.atlassian.com/authorize?' . http_build_query($params);
       
        return $url;
    }

    public function getToken($code)
    {
        $postData = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->callbackUrl,
        ];

        $response = $this->curl('https://auth.atlassian.com/oauth/token', json_encode($postData), 'POST');
        if (isset($response['access_token'])) {
            $result = $this->db->updateToken($response);
            dd($result);
        }
    }

    public function getRefreshToken()
    {
        $postData = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $this->refreshToken,
        ];

        $response = $this->curl('https://auth.atlassian.com/oauth/token', json_encode($postData), 'POST');
        if (isset($response['refresh_token'])) {
            $result = $this->db->updateToken($response);
        }
    }

    public function getProjects()
    {
        try {
            $prjsArray = [];
            $proj = new ProjectService($this->iss);
            
            $prjs = $proj->getAllProjects();
            foreach ($prjs as $p) {
                $prjsArray[] = sprintf('Project Key:%s, Id:%s, Name:%s, projectCategory: %s\n',
                    $p->key, $p->id, $p->name, $p->projectCategory['name']
                );			
            }	
            return $prjsArray;

        } catch (JiraException $e) {
            print_r("에러 발생 : ".$e->getMessage());
        }
    }

    public function getIssues()
    {
        try {
            $issueService = new IssueService($this->iss);
	
            $queryParam = [
                'fields' => [  // default: '*all'
                    'summary',
                    'comment',
                ],
                'expand' => [
                    'renderedFields',
                    'names',
                    'schema',
                    'transitions',
                    'operations',
                    'editmeta',
                    'changelog',
                ]
            ];
                    
            $issue = $issueService->get('DEV-1016', $queryParam);
            
            dd($issue);
        } catch (JiraException $e) {
            print_r("에러 발생 : ".$e->getMessage());
        }
    }

    public function getComments()
    {
        try {
            $issueService = new IssueService();
    
            $param = [
                'startAt' => 0, 
                'maxResults' => 3,
                'expand' => 'renderedBody',
            ];
        
            $comments = $issueService->getComments('TEST-867', $param);
            
            dd($comments);
        } catch (JiraException $e) {
            print_r("에러 발생 : ".$e->getMessage());
        }
    }

    public function getUsers()
    {
        try {
            $us = new UserService();

            $paramArray = [
                'username' => '.', // get all users. 
                'startAt' => 0,
                'maxResults' => 1000,
                'includeInactive' => true,
                //'property' => '*',
            ];

            // get the user info.
            $users = $us->findUsers($paramArray);
        } catch (JiraException $e) {
            print_r("에러 발생 : ".$e->getMessage());
        }
    }

    protected function curl($url, $data, $type = "GET")
    {
        $headers = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        switch ($type) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                $headers[] = 'Content-type: application/json';
                break;
            case 'GET':
                break;
            case 'PUT':
                $headers[] = 'Content-type: application/json';
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $result = json_decode($result, true);

        if (isset($result['error'])) {
            dd($result);
        }

        curl_close($ch);

        return $result;
    }
}
