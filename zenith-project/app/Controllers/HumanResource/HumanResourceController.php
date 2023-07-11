<?php

namespace App\Controllers\HumanResource;

use App\Controllers\BaseController;
use App\Libraries\Douzone\Douzone;
use App\Models\HumanResource\HumanResourceModel;
class HumanResourceController extends BaseController
{
    protected $hr;

    public function __construct()
    {
        $this->hr = model(HumanResourceModel::class);
    }
    public function humanResource()
    {
        return view('humanResource/humanresource');
    }

    public function getDayOff() { //연차
        $douzone = new Douzone();
        $list = $douzone->getDayOff();
        dd($list);
    }

    public function updateUsersByDouzone() {
        $douzone = new Douzone();
        $list = $douzone->getMemberList();
        foreach($list as $row) {
            $this->hr->updateUserByEmail($row);
        }
    }
}
