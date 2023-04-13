<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;

class WithdrawController extends BaseController
{
    public function withdraw()
    {
        return view('accounting/withdraw/withdraw');
    }

    public function withdrawList()
    {
        return view('accounting/withdraw/withdraw_list');
    }
}
