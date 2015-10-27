<?php

namespace Home\Controller;
use Think\Controller;

class BaseController extends Controller{
    
    protected function _initialize(){
        header("Content-type: text/html; charset=utf-8");
		/*
		if(!isMobile()){
			//header("location:".C('WEB_PC_URL'));
		}*/
		
        //网站配置
        $SiteConfigData =   S('SITE_CONFIG_DATA');
        if(!$SiteConfigData){
            $data = M('Config')->where(array('group'=>1))->select();
            $arr = array();
            foreach ($data as $key => $value){
                $arr[$value['keys']] = $value['value'];
            }
            S('SITE_CONFIG_DATA', $arr, C('APP_S_CACHE_TIME'));
			$SiteConfigData =   $arr;
        }
        
        //缓存 城市列表 品牌 车系
        $SiteCityData = S('SITE_CITY_DATA');
        if(!$SiteCityData){
            $data = M('City')->order('sort desc,id asc')->select();
            S('SITE_CITY_DATA', $data, C('APP_S_CACHE_TIME'));
			$SiteCityData = $data;
            foreach ($data as $key=>$value){
                $brand = array();
                $series = array();
                $carList = M('Car')->where('status in (1,2) and city_id ='.$value['id'])->field('brand_id,series_id,city_id')->select();
                foreach ($carList as $k=>$v){
                   $brand[]  =  $v['brand_id'];
                   $series[] =  $v['series_id'];
                }
                S('SITE_CAR_BRAND_'.$value['id'], $brand, C('APP_S_CACHE_TIME'));
                S('SITE_CAR_SERIES_'.$value['id'], $series, C('APP_S_CACHE_TIME'));
            }
        }
        
        
        //缓存 店铺 品牌 车系
        $SiteStoreData = S('SITE_STORE_DATA');
        if(!$SiteStoreData){
            $data = M('Store')->order('sort desc,id asc')->select();
            S('SITE_STORE_DATA', $data, C('APP_S_CACHE_TIME'));
			$SiteStoreData = $data;
            foreach ($data as $key=>$value){
                $brand = array();
                $series = array();
                $carList = M('Car')->where('status in (1,2) and store_id ='.$value['id'])->field('brand_id,series_id,store_id')->select();
                
                foreach ($carList as $k=>$v){
                   $brand[]  =  $v['brand_id'];
                   $series[] =  $v['series_id'];
                }
                S('SITE_CAR_BRAND_STORE_'.$value['id'], $brand, C('APP_S_CACHE_TIME'));
                S('SITE_CAR_SERIES_STORE_'.$value['id'], $series, C('APP_S_CACHE_TIME'));
            }
        }
        
        //获取当前城市
        $subDomian = extend($_SERVER['HTTP_HOST']);
		$cookiewebcity = cookie('webcity');
		$sessionwebcity =  session('webcity');
        $webCity = empty($cookiewebcity) ? $sessionwebcity : $cookiewebcity;
		session('webcity', $webCity);
        if(!empty($_GET['city']) || !empty($_GET['store'])){
            if($_GET['city'] == 'all'){
                session('webcity', null);
                session('webstore', null);
                cookie('webcity', null);
            }else{
                $city = M('City')->field('id,title,pinyin')->where('id = '.$_GET['city'])->find();
                $store = M('Store')->field('id,title')->where('id = '.$_GET['store'])->find();
                $city ? session('webcity', $city) : session('webcity', null);
                $store ? session('webstore', $store) : session('webstore', null);
                $city ? cookie('webcity', $city) : cookie('webcity', null);
            }
        }else{
            if(!isset($webCity)){
                $ipData = curl_get_contents(C('IP_LOCATION_URL').get_client_ip());
                $city = json_decode($ipData,true);
                //$data = M('City')->field('id,title,pinyin')->where(array( 'title'=> $city['city'] ))->find();
                $data = M('City')->field('id,title,pinyin')->where(array( 'title'=> '深圳' ))->find();
                $data ? session('webcity', $data) : session('webcity', null);
                $data ? cookie('webcity', $data) : cookie('webcity', null);
            }
        }
        

        
        $this->assign(array(
            'SiteConfigData' => $SiteConfigData,
            'SiteCityData'  => $SiteCityData,
            'SiteCurentCity'=> session('webcity')
        ));
    }
    
    /* 空操作，用于输出404页面 */
    public function _empty(){
        $this->redirect('/');
    }
    
}