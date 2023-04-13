<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;

class UnpaidController extends BaseController
{
    public function unpaid()
    {
        return view('accounting/unpaid/unpaid');
    }
}
