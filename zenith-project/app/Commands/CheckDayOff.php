<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\HumanResource\HumanResourceController;
use App\Libraries\slack_api\SlackChat;

class CheckDayOff extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Slack';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'todayDayOff';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = '당일 연차/시차 슬랙 전송';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'command:dayoff [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $sendType = "";
        if(isset($params[0]))
            $sendType = $params[0];
        $dayOff = new HumanResourceController();
        $lists = $dayOff->getTodayDayOff();
        $slack = new SlackChat();
        $msg = [];
        $Tdate = date('Ymd');
        foreach($lists as $row) {
            if(date('H') == 10 || $sendType == 'today') { //업무시작시간에 당일 연/시차 전체 전송
                if(date('Ymd', strtotime($row['start'])) != $Tdate) continue;
            } else { //그 외 매시간 새로 등록된 당일 연/시차 전송
                if(date('Ymd', strtotime($row['start'])) != $Tdate || strtotime($row['datetime']) <= strtotime('-1 hour')) continue;
            }
            if($row['type'] == '시차') {
                $start = date('Y년m월d일 H시부터', strtotime($row['start']));
                $end = date('H시까지', strtotime($row['end']));
                $term = "{$start} {$end}({$row['term']}시간)";
                if(date('H', strtotime($row['start'])) == 10)
                    $term = date('Y년m월d일 H시', strtotime($row['end']))." 출근({$row['term']}시간 사용)";
                if(date('H', strtotime($row['end'])) == 19)
                    $term = date('Y년m월d일 H시', strtotime($row['start']))." 퇴근({$row['term']}시간 사용)";
            } else {
                $start = date('Y년m월d일부터', strtotime($row['start']));
                $end = date('Y년m월d일까지', strtotime($row['end']));
                $term = "{$start} {$end}({$row['term']}일)";
                if($row['start'] == $row['end'])
                    $term = date('Y년m월d일', strtotime($row['start']));
            }
            $msg[] = ["type"=>"divider"];
            $msg[] = [
                "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => "*{$row['name']}* _({$row['position']}-{$row['team']})_\n`[{$row['type']}]` {$term}"
                ]
            ];
        }
        if(!count($msg)) return;
        $data = [
            'channel' => '연차공유',
            'text' => '',
            'blocks' => json_encode($msg,JSON_UNESCAPED_UNICODE)
        ];
        if($sendType == 'debug') dd($data);
        $response = $slack->sendMessage($data);
    }
}
