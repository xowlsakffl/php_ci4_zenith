<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        auth()->user()->addPermission('users.create');
        return Pages::index();
    }
}
