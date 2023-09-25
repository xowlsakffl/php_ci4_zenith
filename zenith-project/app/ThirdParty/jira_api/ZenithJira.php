<?php
namespace App\ThirdParty\jira_api;

require_once __DIR__ . '/vendor/autoload.php';

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

class ZenithJira
{
    //jira api 패키지
    private $iss;
    private $accessToken = "ATATT3xFfGF0VyX1V8f8LBfvLor56_m4j5YwjzThpLImqjCU2GP5kN9h50TxGAF2Eyzp17kNNxTHsRP5ECZkI1idb5cYRuqueGHtt3SBS6xvsMr-XQAFwxcoDeyooYA6jP6ipoC8t-mCgdUn48P-xRjN_unPTY4cZBa7lo_tOSW4k4LfUDGi2Cw=75FDE1D8";
    private $jiraHost = "https://carelabs-dm.atlassian.net";

    //jira rest api
    private $clientId = 'XCmAEFMFAO31jUrdTFRpix2hF0I0ZBxy';
    private $clientSecret = 'ATOAjpOLWcMGH9UmpjtaUyFcpC6hf6HvCrak5FLXeKDuaasPCKwIOD1mFDfQFD29Haoc54E49FB5';
    private $redirectUri = 'https://177f-59-9-155-203.ngrok-free.app/jira/oauth';
    private $token = "eyJraWQiOiJmZTM2ZThkMzZjMTA2N2RjYTgyNTg5MmEiLCJhbGciOiJSUzI1NiJ9.eyJqdGkiOiI2Mjc3ZGJmZS02YmRiLTQ3NTAtYTlkNy1mMjFhN2M1NzgyZDAiLCJzdWIiOiI2M2Q5YmYyMzgyZWVlZTc4YTRjZGU5NTUiLCJuYmYiOjE2OTMxODU2OTUsImlzcyI6Imh0dHBzOi8vYXV0aC5hdGxhc3NpYW4uY29tIiwiaWF0IjoxNjkzMTg1Njk1LCJleHAiOjE2OTMxODkyOTUsImF1ZCI6IlhDbUFFRk1GQU8zMWpVcmRURlJwaXgyaEYwSTBaQnh5IiwiaHR0cHM6Ly9hdGxhc3NpYW4uY29tL2VtYWlsRG9tYWluIjoiY2FyZWxhYnMuY28ua3IiLCJodHRwczovL2lkLmF0bGFzc2lhbi5jb20vdWp0IjoiNTIzNWZiMjQtZDEyMC00NGUwLTkyMTQtMmE2N2E2ZWM5Y2EyIiwiY2xpZW50X2lkIjoiWENtQUVGTUZBTzMxalVyZFRGUnBpeDJoRjBJMFpCeHkiLCJodHRwczovL2lkLmF0bGFzc2lhbi5jb20vYXRsX3Rva2VuX3R5cGUiOiJBQ0NFU1MiLCJodHRwczovL2F0bGFzc2lhbi5jb20vZmlyc3RQYXJ0eSI6ZmFsc2UsImh0dHBzOi8vYXRsYXNzaWFuLmNvbS92ZXJpZmllZCI6dHJ1ZSwiaHR0cHM6Ly9pZC5hdGxhc3NpYW4uY29tL3Nlc3Npb25faWQiOiI0YWY1N2U5NS02ZjYwLTQ4NTMtYjg2MC01YmQ1OGNmNzgwYTEiLCJodHRwczovL2lkLmF0bGFzc2lhbi5jb20vcHJvY2Vzc1JlZ2lvbiI6InVzLWVhc3QtMSIsImh0dHBzOi8vYXRsYXNzaWFuLmNvbS9zeXN0ZW1BY2NvdW50RW1haWwiOiI3MDYzZWUzNS03NDA5LTRlYTItYjU5Ny01M2M5YzRkYjU4OWJAY29ubmVjdC5hdGxhc3NpYW4uY29tIiwiY2xpZW50X2F1dGhfdHlwZSI6IlBPU1QiLCJzY29wZSI6InJlYWQ6amlyYS13b3JrIiwiaHR0cHM6Ly9hdGxhc3NpYW4uY29tLzNsbyI6dHJ1ZSwiaHR0cHM6Ly9pZC5hdGxhc3NpYW4uY29tL3ZlcmlmaWVkIjp0cnVlLCJodHRwczovL2F0bGFzc2lhbi5jb20vb3JnSWQiOiIyODVjZDNjYS04MzQ4LTFhYjQtazhhai1iajg5ODdqM2JrNjUiLCJodHRwczovL2F0bGFzc2lhbi5jb20vb2F1dGhDbGllbnRJZCI6IlhDbUFFRk1GQU8zMWpVcmRURlJwaXgyaEYwSTBaQnh5IiwiaHR0cHM6Ly9hdGxhc3NpYW4uY29tL3N5c3RlbUFjY291bnRFbWFpbERvbWFpbiI6ImNvbm5lY3QuYXRsYXNzaWFuLmNvbSIsImh0dHBzOi8vYXRsYXNzaWFuLmNvbS9zeXN0ZW1BY2NvdW50SWQiOiI3MTIwMjA6Y2EzM2QxODEtMmExNy00Y2FkLWEwMDMtMGVhZWM5ZGI0MDcyIn0.XCZjU2azwVmsrBWh4toi_ZiXYpyqZhjawGP9nE6_P3Yu0mUn_frEb2M5dsVzYBjrJWA4wWzvtTu-QAB4Cj9ZLAya5EuE8c6XANJuJxiZ7bygZhAVn4HB9yKmkM9SWD-RVZW_58ELS9z1_Y0lYXTxgb_CiXLkMGnGHlKS37azbWU0kYI0PY-y_l-OWl8O1uOTfgHORVRxN6T5dXC-DA3rTyLtlEF64f0UdVEVT1YQxllhZ7J2DpYJ4OeJE1TRcfNPzuvD9BJuWfJi2PUse1FhFYjMVnuy1BSY1wpQJmYg9sT8ZYfCLUDwqWaPiu5US_u3lEsdzz36aKgRQW4QzPdgBA";
    public function __construct()
    {
        $this->iss = new ArrayConfiguration(
            [
                 'jiraHost' => $this->jiraHost,
                 'useTokenBasedAuth' => true,
                 'personalAccessToken' => $this->accessToken,
                  
                  // custom log config
                 'jiraLogEnabled' => true,
                 'jiraLogFile' => "my-jira-rest-client.log",
                 'jiraLogLevel' => 'INFO',

            ]
        );
    }

    public function getProjects()
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
    }

    /* public function getAuthorizationCode()
    {
        $data = array(
            'audience' => 'api.atlassian.com',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => 'read:jira-work',
            'response_type' => 'code',
            'prompt' => 'consent'
        );
        $data = http_build_query($data);
        $url = 'https://auth.atlassian.com/authorize?' . $data;

        return $url;
    }

    public function getAccessToken($code)
    {
        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
        ];
        $result = $this->curl('https://auth.atlassian.com/oauth/token', null, $data, 'POST');
        dd($result);
    }
      
    protected function curl($url, $api_key, $data, $type = "GET")
    {
        if ($api_key != NULL)
            $headers = array("Authorization: Bearer {$api_key}");
        else
            $headers = array();

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        switch ($type) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'GET':
                break;
            case 'PUT':
                $headers[] = 'Content-type: application/json';
            default:
                $headers[] = 'Content-type: application/json';
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        //curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $result = curl_exec($ch);
        $result = json_decode($result, true);

        curl_close($ch);

        return $result;
    } */
}
