<?php

namespace App\Controllers\HumanResource;

use App\Controllers\BaseController;

class HumanResourceController extends BaseController
{
    public function humanResource()
    {
        return view('humanResource/humanresource');
    }
}
