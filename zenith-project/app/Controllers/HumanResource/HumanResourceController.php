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

    private function getDayOff() { //연차
        $douzone = new Douzone();
        $list = $douzone->getDayOff();
        
        return $list;
    }

    public function getMemberList() {
        $lists = $this->hr->getMemberList();
        foreach($lists as $row) $data[] = [$row['nickname'],$row['division'],$row['secret']];
        dd($data);
        return $lists;
    }

    private function getHourTicketUse() {
        $hourticket = $this->hr->getHourTicketUse();

        return $hourticket;
    }

    public function updateUsersByDouzone() {
        $douzone = new Douzone();
        $list = $douzone->getMemberList();
        foreach($list as $row) {
            $this->hr->updateUserByEmail($row);
        }
    }

    public function getTodayDayOff() {
        $list = $this->getDayOff();
        $data = [];
        foreach($list as $row) {
            if(date('Y-m-d', strtotime($row['start'])) >= date('Y-m-d')) {
                $data['day'][] = $row;
            }
        }
        $list = $this->getHourTicketUse();
        foreach($list as $row) {
            if(date('Y-m-d', strtotime($row['date'])) >= date('Y-m-d')) {
                $data['hour'][] = $row;
            }
        }
        $result = $this->setMessageData($data);
        return $result;
    }

    private function setMessageData($lists) {
        $data = [];
        foreach($lists as $type => $list) {
            foreach($list as $row) {
                $user = $this->hr->getUserByEmail($row['email']);
                $type = isset($row['type'])?$row['type']:'시차';
                $start = isset($row['start'])?date('Y-m-d', strtotime($row['start'])):date('Y-m-d H:m', strtotime($row['date']." ".$row['time']));
                $end = isset($row['end'])?date('Y-m-d', strtotime($row['end'])):date('Y-m-d H:m', strtotime($row['date']." ".$row['time']." +{$row['hour_ticket']} hours"));
                $term = isset($row['used'])?$row['used']:$row['hour_ticket'];
                $datetime = isset($row['reg_datetime'])?$row['reg_datetime']:$row['rep_dt'];
                $data[] = [
                    'type' => $type,
                    'name' => $user['nickname'],
                    'division' => $user['division'],
                    'team' => $user['team'],
                    'position' => $user['position'],
                    'term' => $term,
                    'start' => $start,
                    'end' => $end,
                    'datetime' => $datetime
                ];
            }
        }
        return $data;
    }
}
