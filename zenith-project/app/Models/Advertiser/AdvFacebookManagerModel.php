<?php

namespace App\Models\Advertiser;

use CodeIgniter\Model;

class AdvFacebookManagerModel extends Model
{
    protected $facebook, $ro_facebook;
    public function __construct()
    {
        $this->facebook = \Config\Database::connect('facebook');
        $this->ro_facebook = \Config\Database::connect('ro_facebook');
    }

    public function getAdAccounts()
    {
        $builder = $this->facebook->table('fb_ad_account');
        $builder->select("*");
        $builder->where('status', 1);
        $builder->where('perm', 1);
        $builder->where('pixel_id IS NOT NULL');
        $result = $builder->get()->getResultArray();
        
        return $result;
    }
    
    public function getChartReport()
    {
        
    }
}
