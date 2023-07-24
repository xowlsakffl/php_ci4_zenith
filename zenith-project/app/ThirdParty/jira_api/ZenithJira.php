<?php
namespace App\ThirdParty\jira_api;

require_once __DIR__ . '/vendor/autoload.php';

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\JiraException;

class ZenithJira
{
    private $iss;
    private $accessToken = "ATATT3xFfGF0cODEbaxMki0WpU8XddAW-oY3BdWN5rMGIyMUyNuqNmcQ5-bk7fVu60N54VhYtTCEGJD-tY4C3srrrL8mQMt2gIP34DXtA3iEUJ-RZqixoEdLL7KhuLjODJzLaeuSJmm4KGVj_N9eyabWS4NsvXHKrfH2cFnq0m33TK85SARpg74=E87D04A7";
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

        } catch (JiraRestApi\JiraException $e) {
            print('Error Occured! ' . $e->getMessage());
        }
    }
}
