<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */
service('auth')->routes($routes, ['except' => ['magic-link']]);
$routes->get('login/magic-link', '\App\Controllers\Auth\MagicLinkController::loginView', ['as' => 'magic-link']);
$routes->post('login/magic-link', '\App\Controllers\Auth\MagicLinkController::loginAction');
$routes->get('login/verify-magic-link', '\App\Controllers\Auth\MagicLinkController::verify', ['as' => 'verify-magic-link']);
$routes->get('set-password', '\App\Controllers\Auth\PasswordChangeController::changePasswordView', ['as' => 'set_password']);
$routes->post('set-password', '\App\Controllers\Auth\PasswordChangeController::changePasswordAction', ['as' => 'set_password_action']);
// We get a performance increase by specifying the default
// route since we don't have to scan directories.

//게스트 - 승인대기중 페이지
$routes->group('', ['filter' => 'group:admin,superadmin,developer,guest'], static function($routes){
    $routes->get('guest', 'GuestController::index', ['as' => 'guest']);
});

//관리자, 최고관리자, 개발자, 일반사용자, 광고주, 광고대행사
$routes->group('', ['filter' => 'group:admin,superadmin,developer,user,agency,advertiser'], static function($routes){
    $routes->get('mypage', 'User\UserController::myPage');
    $routes->post('mypage/update', 'User\UserController::myPageUpdate');
    $routes->get('password-changed-at', 'User\UserController::setPasswordChangedAtAjax');

    $routes->group('', ['filter' => 'group:superadmin,admin,developer,user'], static function($routes){
        $routes->get('/', 'HomeController::index');
        $routes->get('/home', 'HomeController::index');
        $routes->get('/home/report', 'HomeController::getReports');
        $routes->get('pages/(:any)', 'PageController::view/$1');
    });

    //광고주, 광고대행사 관리
    $routes->group('', ['filter' => 'group:superadmin,admin,developer,user', 'permission:admin.access,admin.settings,agency.access'], static function($routes){
        $routes->get('company', 'Company\CompanyController::index');
        $routes->get('company/get-companies', 'Company\CompanyController::getCompanies');
        $routes->get('company/get-company', 'Company\CompanyController::getCompany');
        $routes->get('company/get-search-agencies', 'Company\CompanyController::getSearchAgencies');
        $routes->post('company/create-company', 'Company\CompanyController::createCompany');
        $routes->put('company/set-company', 'Company\CompanyController::setCompany');
        $routes->delete('company/delete-company', 'Company\CompanyController::deleteCompany');

        $routes->get('company/get-search-users', 'User\UserController::getSearchUsers');
        $routes->get('company/get-belong-users', 'User\UserController::getBelongUsers');
        $routes->put('company/set-belong-user', 'User\UserController::setBelongUser');
        $routes->delete('company/except-belong-user', 'User\UserController::exceptBelongUser');

        $routes->get('company/get-search-adaccounts', 'Company\CompanyController::getSearchAdAccounts');
        $routes->get('company/get-company-adaccounts', 'Company\CompanyController::getCompanyAdAccounts');
        $routes->put('company/set-adaccounts', 'Company\CompanyController::setCompanyAdAccount');
        $routes->delete('company/except-company-adaccount', 'Company\CompanyController::exceptCompanyAdAccount');
    });

    // 회원 관리
    $routes->group('', ['filter' => 'group:admin,superadmin', 'permission:admin.access,admin.settings'], static function($routes){
        $routes->get('user', 'User\UserController::index');
        $routes->get('user/get-users', 'User\UserController::getUsers');
        $routes->get('user/get-user', 'User\UserController::getUser');
        $routes->get('company/get-search-companies', 'Company\CompanyController::getSearchCompanies');
        $routes->put('company/set-user', 'User\UserController::setUser');
    });

    // 광고관리
    $routes->group('advertisements', ['filter' => 'group:superadmin,admin,developer,user'], static function($routes){ 
        $routes->get('', 'AdvertisementManager\AdvManagerController::index');
        $routes->get('data', 'AdvertisementManager\AdvManagerController::getData');
        $routes->get('report', 'AdvertisementManager\AdvManagerController::getReportData');
        $routes->get('accounts', 'AdvertisementManager\AdvManagerController::getAccountsData');
        $routes->get('mediaAccounts', 'AdvertisementManager\AdvManagerController::getMediaAccountsData');
        $routes->get('check-data', 'AdvertisementManager\AdvManagerController::getCheckData');
        $routes->get('diff-report', 'AdvertisementManager\AdvManagerController::getDiffReport');
        $routes->get('get-adv', 'AdvertisementManager\AdvManagerController::getAdvs');
        $routes->get('adaccounts', 'AdvertisementManager\AdvManagerController::getOnlyAdAccount');
        $routes->put('set-dbcount', 'AdvertisementManager\AdvManagerController::setDbCount');
        $routes->put('set-exposed', 'AdvertisementManager\AdvManagerController::setExposed');
        $routes->put('set-status', 'AdvertisementManager\AdvManagerController::updateStatus');
        $routes->put('set-name', 'AdvertisementManager\AdvManagerController::updateName');
        $routes->put('set-budget', 'AdvertisementManager\AdvManagerController::updateBudget');
        $routes->put('set-bidamount', 'AdvertisementManager\AdvManagerController::updateBidAmount');
        $routes->put('set-adv', 'AdvertisementManager\AdvManagerController::updateAdv');
        $routes->put('set-code', 'AdvertisementManager\AdvManagerController::updateCode');
        $routes->get('getmemo', 'AdvertisementManager\AdvManagerController::getMemo');
        $routes->post('addmemo', 'AdvertisementManager\AdvManagerController::addMemo');
        $routes->post('checkmemo', 'AdvertisementManager\AdvManagerController::checkMemo');
        $routes->get('change-log', 'AdvertisementManager\AdvManagerController::getChangeLogs');
        
        $routes->group('facebook', static function($routes){
            $routes->get('report', 'AdvertisementManager\AdvFacebookManagerController::getReport');
        });

        $routes->group('kakao', static function($routes){
            $routes->get('report', 'AdvertisementManager\AdvKakaoManagerController::getReport');
        });

        $routes->group('google', static function($routes){
            $routes->get('report', 'AdvertisementManager\AdvGoogleManagerController::getReport');
        });
    });

    $routes->group('advertisement-etc', ['filter' => 'group:superadmin,admin,developer,user'], static function($routes){ 
        $routes->get('', 'AdvertisementManager\AdvEtcManagerController::index');
    });

    //자동화
    $routes->group('automation', ['filter' => 'group:superadmin,admin,developer,user'], static function($routes){ 
        $routes->get('', 'AdvertisementManager\Automation\AutomationController::index');
        $routes->get('list', 'AdvertisementManager\Automation\AutomationController::getList');
        $routes->get('adv', 'AdvertisementManager\Automation\AutomationController::getAdv');
        $routes->put('set-status', 'AdvertisementManager\Automation\AutomationController::setStatus');
        $routes->get('get-automation', 'AdvertisementManager\Automation\AutomationController::getAutomation');
        $routes->get('logs', 'AdvertisementManager\Automation\AutomationController::getLogs');
        $routes->get('log', 'AdvertisementManager\Automation\AutomationController::getLogByAdv');
        $routes->post('create', 'AdvertisementManager\Automation\AutomationController::createAutomation');
        $routes->post('copy', 'AdvertisementManager\Automation\AutomationController::copyAutomation');
        $routes->put('update', 'AdvertisementManager\Automation\AutomationController::updateAutomation');
        $routes->delete('delete', 'AdvertisementManager\Automation\AutomationController::deleteAutomation');
    });
    
    // 통합 DB관리
    $routes->group('integrate', static function($routes){   
        $routes->get('', 'Integrate\IntegrateController::index');
        $routes->get('list', 'Integrate\IntegrateController::getList');
        $routes->get('lead', 'Integrate\IntegrateController::getLead');
        $routes->get('leadcount', 'Integrate\IntegrateController::getEventLeadCount');
        $routes->get('statuscount', 'Integrate\IntegrateController::getStatusCount');
        $routes->get('getmemo', 'Integrate\IntegrateController::getMemo');
        $routes->post('addmemo', 'Integrate\IntegrateController::addMemo');
        $routes->post('setstatus', 'Integrate\IntegrateController::setStatus');
    });

    // 회계 관리
    $routes->group('accounting', ['filter' => 'group:superadmin,admin,developer,user'], static function($routes){   
        $routes->get('tax', 'Accounting\TaxController::tax');
        $routes->get('taxList', 'Accounting\TaxController::taxList');
        $routes->get('unpaid', 'Accounting\UnpaidController::unpaid');
        $routes->get('withdraw', 'Accounting\WithdrawController::withdraw');
        $routes->get('withdrawList', 'Accounting\WithdrawController::withdrawList');
    });

    // 인사 관리
    $routes->group('humanresource', ['filter' => 'group:superadmin,admin,developer,user'], static function($routes){   
        $routes->get('management', 'HumanResource\HumanResourceController::humanResource');
    });

    // 이벤트
    $routes->group('eventmanage', ['filter' => 'group:superadmin,admin,developer,user'], static function($routes){   
        $routes->group('event', static function($routes){   
            $routes->get('', 'EventManage\EventController::index');
            $routes->get('list', 'EventManage\EventController::getList');
            $routes->get('adv', 'EventManage\EventController::getAdv');
            $routes->get('media', 'EventManage\EventController::getMedia');
            $routes->post('create', 'EventManage\EventController::createEvent');
            $routes->post('copy', 'EventManage\EventController::copyEvent');
            $routes->put('update', 'EventManage\EventController::updateEvent');
            $routes->get('view', 'EventManage\EventController::getEvent');
            $routes->delete('delete', 'EventManage\EventController::deleteEvent');
            $routes->get('impressions', 'EventManage\EventController::getEventImpressions');
        });

        $routes->group('advertiser', static function($routes){   
            $routes->get('', 'EventManage\AdvertiserController::index');
            $routes->get('list', 'EventManage\AdvertiserController::getList');
            $routes->get('view', 'EventManage\AdvertiserController::getAdvertiser');
            $routes->get('company', 'EventManage\AdvertiserController::getCompanies');
            $routes->post('create', 'EventManage\AdvertiserController::createAdv');
            $routes->put('update', 'EventManage\AdvertiserController::updateAdv');
        });
        
        $routes->group('media', static function($routes){   
            $routes->get('', 'EventManage\MediaController::index');
            $routes->get('list', 'EventManage\MediaController::getList');
            $routes->get('view', 'EventManage\MediaController::getMedia');
            $routes->post('create', 'EventManage\MediaController::createMedia');
            $routes->put('update', 'EventManage\MediaController::updateMedia');
        });

        $routes->group('change', static function($routes){   
            $routes->get('', 'EventManage\ChangeController::index');
            $routes->get('list', 'EventManage\ChangeController::getList');
            $routes->get('view', 'EventManage\ChangeController::getChange');
            $routes->post('create', 'EventManage\ChangeController::createChange');
            $routes->put('update', 'EventManage\ChangeController::updateChange');
        });

        $routes->group('blacklist', static function($routes){   
            $routes->get('', 'EventManage\BlackListController::index');
            $routes->get('list', 'EventManage\BlackListController::getList');
            $routes->get('view', 'EventManage\BlackListController::getBlackList');
            $routes->post('create', 'EventManage\BlackListController::createBlackList');
            $routes->delete('delete', 'EventManage\BlackListController::deleteBlackList');
        });

        $routes->group('excel', static function($routes){   
            $routes->get('', 'EventManage\ExcelController::index');
            $routes->post('upload', 'EventManage\ExcelController::upload');
        });
        
    });

    $routes->group('eventmanage', ['filter' => 'group:superadmin,admin,developer,user'], static function($routes){
        $routes->get('/advertisement/(:any)', 'Advertisement\ApiController::$1');

        $routes->get('fbapi/(:any)', 'Advertisement\Facebook::$1');

        $routes->get('ggapi/(:any)', 'Advertisement\GoogleAds::$1');

        $routes->get('example/(:any)', 'ExampleController::view/$1');

        $routes->get('calendar', 'Calendar\CalendarController::index');
    });
});

$routes->cli('fbapi/(:any)', 'Advertisement\Facebook::$1');
$routes->cli('kmapi/(:any)', 'Advertisement\KakaoMoment::$1');
$routes->cli('ggapi/(:any)', 'Advertisement\GoogleAds::$1');

//잠재고객 가져오기
$routes->cli('sendToEventLead', 'Advertisement\AdLeadController::sendToEventLead');

//자동화 실행
$routes->cli('automation/exec', 'AdvertisementManager\Automation\AutomationController::automation');
$routes->get('automation/exec', 'AdvertisementManager\Automation\AutomationController::automation');
/* $routes->get('slack/code', '\App\ThirdParty\botman\ChatBot::getCode');
$routes->get('auth/slack/callback', '\App\ThirdParty\botman\ChatBot::getToken');
$routes->match(['get', 'post'], 'slack/test', '\App\ThirdParty\botman\ChatBot::test'); */

$routes->match(['get', 'post'], 'slack/(:any)', '\App\Libraries\slack_api\SlackChat::$1');
$routes->cli('slack/(:any)', '\App\Libraries\slack_api\SlackChat::$1');
$routes->get('dz/(:any)', '\App\Libraries\Douzone\Douzone::$1');
$routes->cli('dz/(:any)', '\App\Libraries\Douzone\Douzone::$1');

$routes->cli('hr/(:any)', 'HumanResource\HumanResourceController::$1');
$routes->get('hr/(:any)', 'HumanResource\HumanResourceController::$1');

$routes->match(['get', 'post'], 'jira/(:any)', 'Api\JiraController::$1');
$routes->match(['get', 'post'], 'interlock/(:any)', 'Api\TestController::$1');

$routes->get('resta/get-adv', 'Api\RestaController::getList');

