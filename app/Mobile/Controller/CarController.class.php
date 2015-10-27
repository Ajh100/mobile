<?php

namespace Mobile\Controller;
use Think\Controller;

class CarController extends BaseController{
    
    public function index(){
        $webcity = session('webcity');
        $webstore = session('webstore');
        $map['status'] = array('in', '1,2');
        if(!empty($webcity)){
            $map['city_id'] = $webcity['id'];
        }
        if(!empty($webstore)){
            $map['store_id'] = $webstore['id'];
			$store = M('Store')->where('id='.$webstore['id'])->find();
			$this->assign('storetel', $store['tel']);
        }
        if(!empty($_GET['keyword'])){
            $map['title'] = array('like', '%'.I('get.keyword').'%');
        }
        if(!empty($_GET['brand'])){
            $map['brand_id'] = I('get.brand');
        }
        if(!empty($_GET['series'])){
            $map['series_id'] = I('get.series');
        }
        if(!empty($_GET['level'])){
            $map['level_id'] = I('get.level');
        }
        if(!empty($_GET['price'])){
            $price = explode('-', I('get.price'));
            if(intval($price[1]) == 0){
                $map['price'] = array('egt', $price[0]);
            }else{
                $map['price'] = array(array('egt',$price[0]),array('elt',$price[1]), 'and');
            }
        }
        
        $carList = M('Car')->where($map)->order('addtime desc,clicktimes desc')->limit(5)->select();
        $redLevel = M('CarLevel')->where('is_red = 1')->order('sort desc')->limit(6)->select();
        
		if(!$carList){
			unset($map['city_id']);
			$carList =  M('Car')->where($map)->order('addtime desc,clicktimes desc')->select();
			$this->assign('isOther','true');
		}
		
        $this->assign(array(
            'redLevel' => $redLevel,
            'carList' => $carList,
            'CurentStore' => $webstore
        ));
        $this->display();
    }
    
    
    
    public function ajaxlist(){
        if(IS_AJAX){
            $webcity = session('webcity');
            $webstore = session('webstore');
            $map['status'] = array('in', '1,2');
            if(!empty($webcity)){
                $map['city_id'] = $webcity['id'];
            }
            if(!empty($webstore)){
                $map['store_id'] = $webstore['id'];
            }
            if(!empty($_GET['keyword'])){
                $map['title'] = array('like', '%'.$_GET['keyword'].'%');
            }
            if(!empty($_GET['brand'])){
                $map['brand_id'] = I('get.brand');
            }
            if(!empty($_GET['series'])){
                $map['series_id'] = I('get.series');
            }
            if(!empty($_GET['level'])){
                $map['level_id'] = I('get.level');
            }
            if(!empty($_GET['price'])){
                $price = explode('-', I('get.price'));
                if(intval($price[1]) == 0){
                    $map['price'] = array('egt', $price[0]);
                }else{
                    $map['price'] = array(array('egt',$price[0]),array('elt',$price[1]), 'and');
                }
            }
            
            $page = intval($_GET['page']);
            $carList =  M('Car')->where($map)->order('addtime desc,clicktimes desc')->page($page.',5')->select(); 
			
            echo json_encode($carList);            
        }
        exit();
    }

    public function detail($id){
       
        $carData = M('Car')->where('id ='.$id)->find();
        
        if(!$carData){
            $this->redirect(__APP__.'/car/');
        }     
        if(!in_array($carData['status'], array(1,2))){
            $this->redirect(__APP__.'/car/');
        }
        $saleMan = M('Saleman')->find($carData['uid']);
        
        
        $carBrand  =  M('CarBrand')->where('id ='.$carData['brand_id'])->find();
        $carClass  =  M('CarClass')->where('id ='.$carData['class_id'])->find();
        $carEsid   =  M('CarEsid')->where('id ='.$carData['esid_id'])->find();
        $carColor  =  M('CarColor')->where('id ='.$carData['color_id'])->find();
        $storeInfo =  M('Store')->where('id ='.$carData['store_id'])->find();
        
        $webcity = session('webcity');; 
        $redMap = array(
            'price' => array(array('egt', intval($carData['price'])-15), array('elt', intval($carData['price'])+15), 'and'),
            'id' =>  array('neq',$carData['id']),
            'status' => array('in', '1,2')
        );
        if(!empty($webstore)){
            $redMap['city_id'] = $webcity['id'];
        }
        $redCarList = M('Car')->where($redMap)->limit(4)->select();
        
        $cookieUser = explode('-', cookie('user_cars'));
        if(!in_array($id, $cookieUser)){
            M('Car')->where(array('id'=>$id))->setInc('clicktimes',1);
            cookie('user_cars', $id.'-'.cookie('user_cars'), 3600);
        }
        
        //接入微信
        /*
        $jssdk = new JSSDK("wx1151866347a84fcb", "bb430075f413abb20865537ebece2959");
        $signPackage = $jssdk->GetSignPackage();
        $this->assign('signPackage',$signPackage);
        */
        
        $this->assign(array(
            'carData' => $carData,
            'carImg'  => json_decode($carData['imglist'], true),
            'carBrand'=> $carBrand,
            'carClass'=> $carClass,
            'carEsid' => $carEsid,
            'carColor'=> $carColor,
            'saleMan' => $saleMan,
            'storeInfo' => $storeInfo,
            'storeImg' => json_decode($storeInfo['imglist'], true),
            'redCarList' => $redCarList
        ));
        $this->display();
    }
    
}