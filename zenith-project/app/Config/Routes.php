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
service('auth')->routes($routes);
// We get a performance increase by specifying the default
// route since we don't have to scan directories.

//게스트 - 승인대기중 페이지
$routes->group('', ['filter' => 'group:admin,superadmin,developer,guest'], static function($routes){
    $routes->get('guest', 'GuestController::index', ['as' => 'guest']);
});
//관리자, 최고관리자, 개발자, 일반사용자, 광고주, 광고대행사
$routes->group('', ['filter' => 'group:admin,superadmin,developer,user,agency,advertiser'], static function($routes){
    $routes->get('/', 'HomeController::index');
    $routes->get('/home', 'HomeController::index');
    $routes->get('pages/(:any)', 'PageController::view/$1');
    // 회원 관리
    $routes->group('users', static function($routes){
        $routes->get('', 'Api\ApiUserController::get');
        $routes->get('(:num)', 'Api\ApiUserController::$1');
        $routes->put('(:num)', 'Api\ApiUserController::$1');
        $routes->delete('(:num)', 'Api\ApiUserController::$1');
    });

    $routes->group('', ['filter' => 'group:admin,superadmin', 'permission:admin.access,admin.settings'], static function($routes){
        $routes->get('user/list', 'UserController::index');
        $routes->get('user/belong/(:num)', 'UserController::belong/$1');//소속 변경
        $routes->put('user/belong', 'UserController::updateCompanies');
    });
    // 게시판
    $routes->group('boards', static function($routes){     
        $routes->get('', 'Api\ApiBoardController::get');
        $routes->get('(:num)', 'Api\ApiBoardController::$1');
        $routes->post('', 'Api\ApiBoardController::$1');
        $routes->put('(:num)', 'Api\ApiBoardController::$1');
        $routes->delete('(:num)', 'Api\ApiBoardController::$1');
    });   

    $routes->get('board/list', 'BoardController::index');

    // 소속
    $routes->group('companies', static function($routes){     
        $routes->get('', 'Api\ApiCompanyController::get');
        $routes->get('(:num)', 'Api\ApiCompanyController::$1');
        $routes->post('', 'Api\ApiCompanyController::$1');
        $routes->put('(:num)', 'Api\ApiCompanyController::$1');
        $routes->delete('(:num)', 'Api\ApiCompanyController::$1');
    });   

    $routes->group('', ['filter' => 'group:admin,superadmin', 'permission:admin.access,admin.settings'], static function($routes){
        $routes->get('company/list', 'CompanyController::index');
        $routes->get('company/belong/(:num)', 'CompanyController::belong/$1');//소속 변경
        $routes->put('company/belong', 'CompanyController::updateCompanies');
    });

    // 광고관리
    $routes->group('advertisements', static function($routes){ 
        $routes->group('facebook', static function($routes){
            $routes->get('', 'AdvertisementManager\AdvFacebookManagerController::index');
            $routes->get('accounts', 'AdvertisementManager\AdvFacebookManagerController::getAccounts');
            $routes->get('data', 'AdvertisementManager\AdvFacebookManagerController::getData');
        });
        
        
        
        $routes->get('kakao', 'AdvertisementManager\AdvKakaoManagerController::index');

        $routes->get('google', 'AdvertisementManager\AdvGoogleManagerController::index');

        $routes->get('etc', 'AdvertisementManager\AdvEtcManagerController::index');
    });
    
    // 통합 DB관리
    $routes->group('integrate', static function($routes){   
        $routes->get('management', 'Integrate\IntegrateController::index');
        $routes->get('list', 'Integrate\IntegrateController::getList');
        $routes->get('lead', 'Integrate\IntegrateController::getLead');
        $routes->get('leadcount', 'Integrate\IntegrateController::getEventLeadCount');
        $routes->get('statuscount', 'Integrate\IntegrateController::getStatusCount');
    });

    // 회계 관리
    $routes->group('accounting', static function($routes){   
        $routes->get('tax', 'Accounting\TaxController::tax');
        $routes->get('taxList', 'Accounting\TaxController::taxList');
        $routes->get('unpaid', 'Accounting\UnpaidController::unpaid');
        $routes->get('withdraw', 'Accounting\WithdrawController::withdraw');
        $routes->get('withdrawList', 'Accounting\WithdrawController::withdrawList');
    });

    // 인사 관리
    $routes->group('humanresource', static function($routes){   
        $routes->get('management', 'HumanResource\HumanResourceController::humanResource');
    });

    // 이벤트
    $routes->group('eventManage', static function($routes){   
        $routes->get('event', 'EventManage\EventController::event');
        $routes->get('advertiser', 'EventManage\AdvertiserController::advertiser');
        $routes->get('media', 'EventManage\MediaController::media');
        $routes->get('change', 'EventManage\ChangeController::change');
        $routes->get('exel', 'EventManage\ExelController::exel');
    });
});

$routes->get('/advertisement/(:any)', 'Advertisement\ApiController::$1');
$routes->cli('fbapi/(:any)', 'Advertisement\Facebook::$1');
$routes->cli('kmapi/(:any)', 'Advertisement\KakaoMoment::$1');
$routes->cli('ggapi/(:any)', 'Advertisement\GoogleAds::$1');

//잠재고객 가져오기
$routes->cli('sendToEventLead', 'Advertisement\AdLeadController::sendToEventLead');

//테스트
$routes->get('example/(:any)', 'ExampleController::view/$1');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
