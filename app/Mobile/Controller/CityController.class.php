<?php

namespace Mobile\Controller;
use Think\Controller;

class CityController extends BaseController{
    
    public function index(){
        
        $webCity = session('webcity');
        
        $city = S('SITE_CITY_DATA');
        
        $store = M('Store')->where('status = 0')->order('city')->select();
        
        $this->assign(array(
           'city' => $city,
            'store' => $store
        ));        
        $this->display();
    }
}
