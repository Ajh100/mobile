<?php

namespace Mobile\Controller;
use Think\Controller;

class SpecialController extends BaseController{
    
   public function index(){
        $webcity = session('webcity');
        $webstore = session('webstore');
        $map['status'] = 2;
        if(!empty($webcity)){
            $map['city_id'] = $webcity['id'];
        }
        if(!empty($webstore)){
            $map['store_id'] = $webstore['id'];
        }
        $carList =  M('Car')->where($map)->order('addtime desc')->select();
        
        $this->assign(array(
            'carList' => $carList
        ));
        $this->display();
    }
    
}
