<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CompanyController extends BaseController
{
    public function index()
    {
        return view('companies/companies');
    }
}
