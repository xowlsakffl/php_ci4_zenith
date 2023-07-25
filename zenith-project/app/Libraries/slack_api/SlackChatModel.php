<?php
namespace App\Libraries\slack_api;

use CodeIgniter\Model;
Class SlackChatModel extends Model {
    private $zenith;
    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
    }

    public function get_keyword() {
        $builder = $this->zenith->table('slack_keyword');
        $result = $builder->get()->getResultArray();

        return $result;
    }

    public function add_keyword($data) {
        $builder = $this->zenith->table('slack_keyword');
        $result = $builder->setData($data)->upsert();

        return $result;
    }
}