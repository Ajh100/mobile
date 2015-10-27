<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends BaseController{
	
    public function index(){
        
        //首页banner
        //$homeBanner = M('Advert')->where("module = 'mobile_banner'")->order('sort desc')->select();
        
        //推荐品牌
        $redBrand = M('CarBrand')->where('id in (27,37,16,116,39,6)')->select();
        //车辆级别
        $redLevel = M('CarLevel')->where('is_red = 1')->order('sort desc')->limit(6)->select();
        
        //首页新闻
        $redNews = M('Article')->where('is_hot = 1')->order('id desc')->limit(6)->select();
        
        
        //特价车
        $webcity = session('webcity');
        $carMap = array();
        $carMap['status']  = 2;
        if($webcity){
            $carMap['city_id'] = $webcity['id'];
        }
        $speCar = M('Car')->where($carMap)->limit(6)->select();
        
        
        $this->assign(array(
            //'homeBanner' => $homeBanner,
            'redBrand' => $redBrand,
            'redLevel' => $redLevel,
            'redNews' => $redNews,
            'speCar' => $speCar
        ));
	$this->display();
    }
}