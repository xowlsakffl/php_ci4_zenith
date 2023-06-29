<?php

namespace App\Controllers\Slack;

use App\Controllers\BaseController;
use App\ThirdParty\botman\ChatBot;

class SlackController extends BaseController
{
    protected $slackChat;

    public function __construct()
    {
        $this->slackChat = new ChatBot();
        if(!$this->slackChat->token){
            $this->getCode();
        }
    }

    public function getToken()
    {
        $param = $this->request->getGet();
        $result = $this->slackChat->oauth($param);
        dd($result);
    }

    public function getCode()
    {
        $redirectUrl = $this->slackChat->get_code();
        return redirect()->to($redirectUrl);
    }

    public function test()
    {
        $this->slackChat->test();
    }

    public function sendMessage()
    {
        $message = '안녕';
        $this->slackChat->sendMessage($message);
    }
}
