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
}
