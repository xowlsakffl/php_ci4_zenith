<?php
namespace App\Libraries\Douzone;

use CodeIgniter\Model;
Class DouzoneModel extends Model {
    private $zenith, $gwdb;
    public function __construct()
    {
        $this->zenith = \Config\Database::connect();
        $this->gwdb = \Config\Database::connect('gwdb');
    }

    public function getDayOff() {
        $builder = $this->gwdb->table('viberc_gw AS gw');
        $builder->select("usr.email_addr, gw.*, gwc.doc_contents, gwi.doc_xml");
        $builder->join("v_user_info AS usr", "gw.user_id = usr.emp_seq", "left");
        $builder->join("viberc_gw_contents AS gwc", "gw.doc_id = gwc.doc_id", "left");
        $builder->join("viberc_gw_interlock AS gwi", "gw.doc_id = gwi.doc_id", "left");
        $builder->where("gw.form_id", "18");
        $builder->where("gw.doc_sts <=", 90); //90:결재완료
        $builder->where("gw.rep_dt >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $builder->groupBy("gw.doc_id");
        $builder->orderBy("gw.rep_dt", "desc");
        // dd($builder->getCompiledSelect());
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getMemberList() {
        $builder = $this->gwdb->table('v_user_info AS usr');
        $builder->select("email_addr, substring_index(emp_name, '_',-1) AS name, dpm.dp_name AS position, mobile_tel_num AS phone, path_name");
        $builder->join("t_co_comp_duty_position_multi AS dpm", "usr.dept_duty_code = dpm.dp_seq");
        $builder->like("emp_name", "DM_%");
        $builder->where("erp_emp_num <>", "");
        $builder->where("usr.use_yn", "Y");
        $builder->orderBy("join_day", "ASC");
        $result = $builder->get()->getResultArray();

        return $result;
    }
}