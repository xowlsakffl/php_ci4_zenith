<?php

namespace App\Controllers\EventManage;

use App\Controllers\BaseController;
use App\Models\EventManage\ExcelModel;
use Config\Services;

class ExcelController extends BaseController
{
    protected $excel;
    public function __construct() 
    {
        $this->excel = model(ExcelModel::class);
    }

    public function index()
    {
        return view('events/excelUpload/excel', [
            'validation' => Services::validation(),
        ]);
    }

    public function upload()
    {
        if(strtolower($this->request->getMethod()) === 'post'){
            $validated = $this->validate([
                'upload_file' => [
                    'uploaded[upload_file]',
                    'ext_in[upload_file,csv]',
                    'max_size[upload_file, 4096]',
                ],
            ],
            [  
                'upload_file' => [
                    'uploaded' => '업로드 오류가 발생하였습니다.',
                    'ext_in' => 'csv 파일만 업로드 가능합니다.',
                    'max_size' => '파일 크기는 4MB를 초과할 수 없습니다.'
                ]
                
            ]);

            if (!$validated) {
                return view('events/excelUpload/excel', [
                    'validation' => $this->validator
                ]);
            }

            $file = $this->request->getFile('upload_file');
            $filePath = $file->getPathname();
            $row = 1;		//줄수 초기화
			$idx = 0;		//배열 인덱스
            $fp = fopen($filePath, "r");     
            while (($data = fgetcsv($fp, 1000, ",")) !== false) {    //csv파일열기
				if ($row == 1) {
					$row += 1;
					continue;
				}

				$excel[$idx]['reg_date'] = iconv("EUC-KR", "UTF-8", $data[1]);
				$excel[$idx]['name'] = preg_replace("/[#\&\+%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>\[\]\{\}]/i", "", iconv("EUC-KR", "UTF-8", $data[2]));
				$excel[$idx]['phone'] = strtr(iconv("EUC-KR", "UTF-8", $data[3]), array("+82 " => "0", "-" => ""));		// 전화번호에서 국제번호 및 하이픈 제거
				$excel[$idx]['add1'] = iconv("EUC-KR", "UTF-8", $data[4]); //나이 또는 생년월일
				$excel[$idx]['add2'] = iconv("EUC-KR", "UTF-8", $data[5]);
				$excel[$idx]['add3'] = iconv("EUC-KR", "UTF-8", $data[6]);
				$excel[$idx]['add4'] = iconv("EUC-KR", "UTF-8", $data[7]);
				$excel[$idx]['add5'] = iconv("EUC-KR", "UTF-8", $data[8]);
				$excel[$idx]['branch'] = iconv("EUC-KR", "UTF-8", $data[9]);
				$excel[$idx]['age'] = iconv("EUC-KR", "UTF-8", $data[10]);
				$excel[$idx]['partner_name'] = iconv("EUC-KR", "UTF-8", $data[11]); 	//파트너명
				$excel[$idx]['partner_id'] = iconv("EUC-KR", "UTF-8", $data[12]);  		//파트너아이디
				$excel[$idx]['hospital_name'] = iconv("EUC-KR", "UTF-8", $data[13]);	//코드명
				$excel[$idx]['counsel_memo'] = iconv("EUC-KR", "UTF-8", $data[14]);		//지면명
				$excel[$idx]['paper_code'] = iconv("EUC-KR", "UTF-8", $data[13]);	//코드명
				$excel[$idx]['paper_name'] = iconv("EUC-KR", "UTF-8", $data[14]);		//지면명
                $excel[$idx]['event_seq'] = iconv("EUC-KR", "UTF-8", $data[15]);  	//랜딩번호
				$excel[$idx]['site'] =  iconv("EUC-KR", "UTF-8", $data[16]);		//사이트

				if ($excel[$idx]['name'] && $excel[$idx]['phone']) {
					$status_check = $this->excel->status_check($excel[$idx]['name'], $excel[$idx]['phone'], $excel[$idx]['event_seq']);
					$excel[$idx]['status'] = $status_check['status'];
					$excel[$idx]['status_memo'] = $status_check['status_memo'];
				}

				$idx++;
			}
            session()->setFlashdata('success', 'Success! image uploaded.');
            return redirect()->to(site_url('/events/exelUpload/exel')); 
        }
    }
}
