<?php
namespace App\ThirdParty\jira_api;

require_once __DIR__ . '/vendor/autoload.php';

use Exception;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Transition;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;
use JiraRestApi\Status\StatusService;
use JiraRestApi\User\UserService;

class ZenithJira
{
    //jira api 패키지
    private $iss;
    private $accessToken = "ATATT3xFfGF0Gh6USmXkCmBDSx-1bQ6rfv0j47LdsmFfTw3XjSyBdVPMbk3H5ahoqLbC5lBcrxs__c6W10eZ1aFIpOuSnqUC5WdkMGQ0GtZjIPsS-sZyo9IXzAt2_OikJoP7DaaouGhxdaC1wLrce6uuuyxDtoCLVBG-yotCEnCgQAQAHM0ZcMw=8526015D";
    private $jiraHost = "https://carelabs-dm.atlassian.net";
    private $db;
    
    public function __construct()
    {
        $this->db = new JIRADB();
        $this->iss = new ArrayConfiguration(
            [
                'jiraHost' => $this->jiraHost,
                'jiraUser' => 'jaybe@carelabs.co.kr',
                'jiraPassword' => $this->accessToken,
                /* 'useTokenBasedAuth' => true,
                'personalAccessToken' => $this->accessToken, */
                
                // custom log config
                /* 'jiraLogEnabled' => true,
                'jiraLogFile' => "/my-jira-rest-client.log",
                'jiraLogLevel' => 'INFO', */
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

    public function getDevelopIssues($status)
    {
        try {
            $issueService = new IssueService($this->iss);

            $jql = 'project = "DEV" and status="'.$status.'"';
            $startAt = 0;
            $maxResults = 200;

            $issues = $issueService->search($jql, $startAt, $maxResults);
            
            return $issues->getIssues();
        } catch (JiraException $e) {
            print_r("에러 발생 : ".$e->getMessage());
        }
    }

    public function getIssue($issueKey)
    {
        try {
            $issueService = new IssueService($this->iss);
	
            $queryParam = [
                'expand' => [
                    'renderedFields',
                    'names',
                    'schema',
                    'transitions',
                    'operations',
                    'editmeta',
                    'changelog',
                ],
                'fields' => [
                    'status',
                ]
            ];
                    
            $issue = $issueService->get($issueKey, $queryParam);
            
            return $issue;
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

    public function setIssueStatus($issueKey)
    {
        try {
            $issueService = new IssueService($this->iss);
            $transition = new Transition();

            $issue = $this->getIssue($issueKey);
            $currentIssueStatus = $issue->fields->status->id;
            if($currentIssueStatus == '10132'){return ['result' => false, 'msg' => '이미 완료처리된 이슈입니다.'];}

            $transition->setTransitionId('21');
            $result = $issueService->transition($issueKey, $transition);
            return ['result' => $result, 'msg' => '완료처리 되었습니다.'];
        } catch (JiraException $e) {
            print_r("에러 발생 : ".$e->getMessage());
        }
    }

    public function testRequest()
    {
        try {
            $statusService = new StatusService($this->iss);
            $statuses = $statusService->getAll();
            dd($statuses);
        } catch (JiraException $e) {
            print_r("에러 발생 : ".$e->getMessage());
        }
    }
}
