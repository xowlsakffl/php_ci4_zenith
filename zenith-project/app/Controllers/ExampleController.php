<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ExampleController extends BaseController
{
    public function view($page = 'test')
    {
        if(!is_file(APPPATH . 'Views/example/' . $page . '.php')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
        }

        return view('example/'.$page);
    }
}
