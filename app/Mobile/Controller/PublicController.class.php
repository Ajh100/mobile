<?php

namespace Mobile\Controller;
use Think\Controller;

class PublicController extends BaseController{
    
    public function login(){
        if(IS_POST){
            $phone = I('post.telphone');
            session('userPhone', $phone);
            $this->redirect(__APP__.'/car/');
        }
        $this->display();
    }
}
