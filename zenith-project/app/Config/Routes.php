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
//관리자, 최고관리자, 개발자, 일반사용자
$routes->group('', ['filter' => 'group:admin,superadmin,developer,user'], static function($routes){
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

    $routes->group('', ['filter' => 'group:admin,superadmin'], static function($routes){
        $routes->get('user/list', 'UserController::index');
        $routes->get('user/belong/(:num)', 'UserController::belong/$1');//소속 변경
        $routes->get('user/belong/companies', 'UserController::getCompanies');
        $routes->put('user/belong/companies', 'UserController::updateCompanies');
    });
    // 게시판
    $routes->group('boards', static function($routes){     
        $routes->get('', 'Api\ApiBoardController::get');
        $routes->get('(:num)', 'Api\ApiBoardController::$1');
        $routes->post('', 'Api\ApiBoardController::$1');
        $routes->put('(:num)', 'Api\ApiBoardController::$1');
        $routes->delete('(:num)', 'Api\ApiBoardController::$1');
    });   

    $routes->get('boards-list', 'BoardController::index');

    // 소속
    $routes->group('companies', static function($routes){     
        $routes->get('', 'Api\ApiCompanyController::get');
        $routes->get('(:num)', 'Api\ApiCompanyController::$1');
        $routes->post('', 'Api\ApiCompanyController::$1');
        $routes->put('(:num)', 'Api\ApiCompanyController::$1');
        $routes->delete('(:num)', 'Api\ApiCompanyController::$1');
    });   

    $routes->get('companies-list', 'CompanyController::index');
});

$routes->get('/advertisement/(:any)', 'Advertisement\ApiController::$1');
$routes->cli('fbapi/(:any)', 'Advertisement\Facebook::$1');





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
