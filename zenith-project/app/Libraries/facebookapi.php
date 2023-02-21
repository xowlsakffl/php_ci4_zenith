<?php
class FacebookApi
{
    public function __construct() {
        require_once APPPATH.'third_party/facebook_api/facebook-api.php';
    }
}