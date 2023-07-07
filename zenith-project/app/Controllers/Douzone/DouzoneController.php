<?php
namespace App\Controllers\Douzone;

use App\Controllers\BaseController;
use App\Models\Douzone\DouzoneModel;
use PHPHtmlParser\Dom;

class DouzoneController extends BaseController {
    private $douzone;
    public function __construct()
    {
        $this->douzone = model(DouzoneModel::class);
    }

    public function getDayOff() { //연차
        $list = $this->douzone->getDayOff();
        $result = [];
        foreach($list as $row) {
            $data = [
                'email' => $row['email_addr'],
                'name' => preg_replace('/^.+_(.+)$/', '$1', $row['user_nm']),
                'title' => $row['doc_title'],
                'desc' => $this->getDayOffDesc($row),
                'status' => $row['doc_sts'],
                'reg_time' => $row['rep_dt'],
                'end_time' => $row['end_dt']
            ];
            if(preg_match('/시간차/', $data['title']) || preg_match('/시간차/', $data['desc']['reason'])) continue;
            $result[] = $data;
        }

        return $result;
    }

    private function getDayOffDesc($data) {
        $xml = new DOM;
        $xml->loadStr($data['doc_xml']);
        $contents = new DOM;
        $contents->loadStr($data['doc_contents']);
        $result = [
            'type' => trim($xml->find('div',1)->find('table tbody',0)->find('td',0)->text()), //근태구분
            'start' => trim($xml->find('div',1)->find('table tbody',0)->find('td',1)->text()), //시작일자
            'end' => trim($xml->find('div',1)->find('table tbody',0)->find('td',2)->text()), //종료일자
            'used' => trim($xml->find('div',1)->find('table tbody',0)->find('td',4)->text()), //연차차감
            'reason' => trim($contents->find('table tbody')->find('tr',1)->find('td',1)->innerText()), //사유
        ];
        return $result;
    }

    public function getMemberList() { //그룹웨어 직원 목록
        $list = $this->douzone->getMemberList();
        $result = [];
        foreach($list as $row) {
            $div = explode("|", $row['path_name']);
            $division = $div[1];
            $team = (isset($div[2])?$div[2]:"") .(isset($div[3])?"[{$div[3]}]":"");
            $data = [
                'email' => $row['email_addr'],
                'name' => $row['name'],
                'dp_name' => $row['dp_name'],
                'phone' => $row['phone'],
                'division' => $division,
                'team' => $team
            ];
            $result[] = $data;
        }
        
        return $result;
    }

}