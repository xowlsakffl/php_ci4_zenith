<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;

class ChangeController extends BaseController
{
    public function change()
    {
        return view('/events/change/change');
    }
}
