<?php
namespace App\Controllers;

class PageController extends BaseController
{
    public function index()
    {
        $page = new PageController();
        return $page->view();
    }

    public function view($page = 'home')
    {
        if(!is_file(APPPATH . 'Views/pages/' . $page . '.php')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
        }

        $data['title'] = ucfirst($page);
        $data['password_check'] = false;
        if($page = 'home'){
            $password_check = auth()->user()->getEmailIdentity()->password_changed_at;
            if(empty($password_check)){
                $data['password_check'] = true;
            }else{
                if(strtotime($password_check) < strtotime('-2 weeks')){
                    $data['password_check'] = true;
                }
            }
        }

        return view('pages/'.$page, $data);
    }
}