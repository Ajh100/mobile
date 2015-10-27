<?php

namespace Home\Controller;
use Think\Controller;

class UserController extends BaseController{
   
    public function login(){
        if(IS_POST){
	    	if(empty($_POST['telphone'])){
	    		$this->error('请输入电话号码');
	    	}
	    	if(empty($_POST['password'])){
	    		$this->error('请输入密码');
	    	}

	    	$result = M('User')->where(array(
	    		'moblie' => I('post.telphone'),
	    		'password' => md5(I('post.password'))
	    	))->find();
	    	if($result['status'] == 1){
	    		$this->error('账号已被禁用');
	    	}
	    	if(is_array($result)){
	    		$data = M('User')->save(array(
	    			'last_login_time' => time() 
	    		));
	    		session('realname', $result['nickname']);
	    		session('moblie', $result['moblie']);
	    		$this->success('登录成功', U('index'));
	    	}else{
	    		$this->error('手机或密码错误');
	    	}
        }else{
        	$this->display();
        }
    }
    
    public function logout(){
        
		session('realname', null);
		session('moblie', null);
        $this->success('退出成功', U('index/index', array('logout'=>'yes')));
    }

    public function register(){


    	if(IS_POST){
	    	if(empty($_POST['realname'])){
	    		$this->error('请输入姓名');
	    	}
	    	if(empty($_POST['telphone'])){
	    		$this->error('请输入电话号码');
	    	}
	    	if(empty($_POST['password'])){
	    		$this->error('请输入密码');
	    	}
	    	if($_POST['password'] !== $_POST['repassword']){
	    		$this->error('确认密码不一致');
	    	}

	    	$user = M('User')->where(array('telphone'=>$_POST['telphone']))->count();
	    	if($user>0){
	    		$this->error('手机号已经注册');
	    	}

	    	$result = M('User')->add(array(
	    		'nickname' => I('post.realname'),
	    		'moblie' => I('post.telphone'),
	    		'password' => md5(I('post.password')),
	    		'reg_time' => time()
	     	));

	     	if($result){
	     		$this->success('注册成功',U('user/login'));
	     	}else{
	     		$this->error('注册失败请重试');
	     	}
    	}else{

    		$this->display();
    	}
    	
    }
}
