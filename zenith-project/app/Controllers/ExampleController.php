<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class ExampleController extends BaseController
{
    public function view(string $page = 'test')
    {
        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $page)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $viewPath = APPPATH . 'Views/example/' . $page . '.php';
        if (! is_file($viewPath)) {
            throw PageNotFoundException::forPageNotFound($page);
        }

        return view('example/' . $page);
    }
}
