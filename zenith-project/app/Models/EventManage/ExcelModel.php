<?php

namespace App\Models\EventManage;

use CodeIgniter\Model;

class ExcelModel extends Model
{
    public function status_check($name, $phone, $landing_no)
	{
		$status = 1;
		$msg = '';

		// NAME CHECK
		if (preg_match('/(테스트|테스트|test)/i', $name)) { //테스트 체크
			$status = 7;
			$msg = '테스트';
		} else {
            $builder = $this->db->table('app_subscribe');
            $builder->select('eid');
            $builder->where('group_id', $landing_no);
            $builder->where('name', $name);
            $builder->where('deleted', 0);
            $builder->where('status !=', 0);

            $name_result = $$builder->get();

            if ($name_result->getNumRows()) {
                $status = 2;
                $msg = '이름 중복';
            }
		}
		// RESULT CHECK
		if ($status != 1) {
			$data = ['status' => $status, 'status_memo' => $msg];
			return $data;
		}

		//PHONE CHECK
		$number = substr($phone, 3, 8);
		if (!preg_match('/^[\d]{11}$/', $phone)) {
			$status = 6;
			$msg = '전화번호 길이 불량';
		} else if (in_array($number, ['00000000', '11111111', '22222222', '33333333', '44444444', '55555555', '66666666', '77777777', '88888888', '99999999'])) {
			$status = 6;
			$msg = '8자리 같은번호';
		} else {
			$phone_sql = "SELECT eid FROM app_subscribe WHERE group_id = '$landing_no' AND phone = enc_data('$phone') AND deleted = 0 AND status <> 0 ";
			$phone_result = $this->db->query($phone_sql);
			if ($phone_result->db->num_rows) {
				$status = 2;
				$msg = '전화번호 중복';
			}
		}
		// RESULT CHECK
		if ($status != 1) {
			$data = ['status' => $status, 'status_memo' => $msg];
			return $data;
		}

		$data = ['status' => $status, 'status_memo' => $msg];

		return $data;
	}
}
