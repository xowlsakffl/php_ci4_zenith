<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;

class TaxController extends BaseController
{
    public function tax()
    {
        return view('accounting/tax/tax');
    }

    public function taxList()
    {
        return view('accounting/tax/tax_list');
    }
}
