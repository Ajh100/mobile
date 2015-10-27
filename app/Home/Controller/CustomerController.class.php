<?php

namespace Home\Controller;
use Think\Controller;

class CustomerController extends BaseController{
    
    //卖车
    public function sellcar(){
        if(IS_POST){
            $flag = true;
            $msg = '';

            if(empty($_POST['telphone']) || !preg_phone($_POST['telphone'])){
                $flag = false;
                $msg = '请输入正确的手机号码';
            }

            if(empty($_POST['miles'])){
                $flag = false;
                $msg = '请输入行驶里程';
            }
            if(empty($_POST['title'])){
                $flag = false;
                $msg = '请输入品牌车型';
            }
            
            
            if($flag){
                $ip = get_client_ip();
                $model = M();
                $count = $model->query('select * from tc_car_sell where ip = \''.get_client_ip().'\' and to_days(FROM_UNIXTIME(addtime, \'%Y-%c-%d\')) = to_days(now())');
                if(count($count)>3){
                    $msg = '您今天已经提交三次';
                }else{
                    $ipInfo = new \Org\Net\IpLocation('UTFWry.dat'); 
                    $area = $ipInfo->getlocation($ip);
                    $result = M('CarSell')->add(array(
                        'title' => I('post.title'),
                        'miles' => I('post.miles'),
                        'phone' => I('post.telphone'),
                        'ipaddress' => $area["country"],
                        'ip' => $ip,
                        'network' => $area["area"],
                        'addtime' => time()
                    ));

                    if($result !== false){
                        $this->success('发布成功，工作人员会尽快与您联系', __APP__);
                    }else{
                        $msg = '发布失败请稍后重试……';
                    } 
                }
            }
        }
        $this->assign('msg', $msg);
        $this->display();
    }

    
 
    //车型定制
    public function customized(){
        $flag = true;
        $msg = '';
        
        if(IS_POST){
            if(empty($_POST['telphone']) || !preg_phone($_POST['telphone'])){
                $flag = false;
                $msg = '请输入正确的手机号码';
            }
            if(empty($_POST['price']) || preg_match('/^\d{1,3}$/', $_POST['price'])){
                $flag = false;
                $msg = '请输入正确的预算格式为数字';
            }
            if(empty($_POST['title'])){
                $flag = false;
                $msg = '请输入品牌车型';
            }
            
            if($flag){
                $model = M();
                $count = $model->query('select * from tc_car_buy where ip = \''.get_client_ip().'\' and to_days(FROM_UNIXTIME(addtime, \'%Y-%c-%d\')) = to_days(now())');
                if(count($count)>3){
                    $msg = '您今天已经提交三次';
                }else{
                    $ip = get_client_ip();
                    $ipInfo = new \Org\Net\IpLocation('UTFWry.dat'); 
                    $area = $ipInfo->getlocation($ip); 

                    $carBrand = M('CarBrand')->find(I('get.brand'));

                    $result = M('CarBuy')->add(array(
                        'title' => I('post.title'),
                        'price' => I('post.price'),
                        'phone' => I('post.telphone'),
                        'ipaddress' => $area["country"],
                        'ip' => $ip,
                        'network' => $area["area"],
                        'addtime' => time()
                    ));

                    if($result !== false){
                        $this->success('发布成功，工作人员会尽快与您联系', __APP__);
                    }else{
                        $msg =  '发布失败请稍后重试……';
                    }
                }
            }
        }
        $this->assign('msg', $msg);
        $this->display();
    }
    
}
