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

        return view('pages/'.$page, $data);
    }
}