<?php
namespace App\ThirdParty\jira_api;

require_once __DIR__ . '/vendor/autoload.php';

use CURLFile;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

class ZenithJira
{
    //jira api 패키지
    /* private $iss;
    private $accessToken = "ATATT3xFfGF0VyX1V8f8LBfvLor56_m4j5YwjzThpLImqjCU2GP5kN9h50TxGAF2Eyzp17kNNxTHsRP5ECZkI1idb5cYRuqueGHtt3SBS6xvsMr-XQAFwxcoDeyooYA6jP6ipoC8t-mCgdUn48P-xRjN_unPTY4cZBa7lo_tOSW4k4LfUDGi2Cw=75FDE1D8";
    private $jiraHost = "https://carelabs-dm.atlassian.net"; */

    private $clientId, $clientSecret, $callbackUrl, $scopes;

    public function __construct()
    {
        $this->clientId = '3TUJpCxPIoiOeANfyP8aaXqizS96kbjD';
        $this->clientSecret = 'ATOAkI8U7eoF44QOLAXZ53z1Vsiz9HyjPu37wNH94w6GvqDepZeoyM2IQM9mCM-5bVit49FC1644';
        $this->callbackUrl = 'https://aa96-59-9-155-203.ngrok-free.app/jira/callback'; 
        $this->scopes = 'manage:jira-project write:jira-work';
        /* $this->iss = new ArrayConfiguration(
            [
                 'jiraHost' => $this->jiraHost,
                 'useTokenBasedAuth' => true,
                 'personalAccessToken' => $this->accessToken,
                  
                  // custom log config
                 'jiraLogEnabled' => true,
                 'jiraLogFile' => "my-jira-rest-client.log",
                 'jiraLogLevel' => 'INFO',
            ]
        ); */
    }

    public function getCode()
    {
        $params = [
            'audience' => 'api.atlassian.com',
            'client_id' => $this->clientId,
            'scope' => $this->scopes,
            'redirect_uri' => urlencode($this->callbackUrl),
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
            'redirect_uri' => urlencode($this->callbackUrl),
        ];

        $response = $this->curl('https://auth.atlassian.com/oauth/token', $postData, 'POST');
        dd($response);
    }

    /* public function getProjects()
    {
        try {
            $proj = new ProjectService($this->iss);
            
            $prjs = $proj->getAllProjects();
        
            return $prjs;

        } catch (JiraException $e) {
            print_r("에러 발생 : ".$e->getMessage());
        }
    }

    public function getIssues()
    {
        try {
            $issueService  = new IssueService($this->iss);

            $queryParam = [
                'fields' => [
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
                    
            $issue = $issueService->get('TEST-867', $queryParam);
            
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
    } */

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
