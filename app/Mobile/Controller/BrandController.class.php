<?php

namespace Mobile\Controller;
use Think\Controller;

class BrandController extends BaseController{
   
    public function index(){
        
        $webCity = session('webcity');
        $webStore = session('webstore');
        $map = array();
        if($webCity){
            $siteCarBrand = S('SITE_CAR_BRAND_'.$webCity['id']);
            $map = array(
                'id' => array('in', implode(',', $siteCarBrand))
            );      
        }
        if($webStore){
            $siteCarBrand = S('SITE_CAR_BRAND_STORE_'.$webStore['id']);
            $map = array(
                'id' => array('in', implode(',', $siteCarBrand))
            );      
            
        }
        if(!$webCity && !$webStore){
            $carList = M('Car')->where('status in (1,2)')->field('brand_id')->select();
            $brand = array();
            foreach ($carList as $k=>$v){
                $brand[]  =  $v['brand_id'];
            }
            S('SITE_CAR_BRAND', $brand, C('APP_S_CACHE_TIME'));
            $map = array(
                'id' => array('in', implode(',', $brand))
            );    
            
        }
        
        $brandList = M('CarBrand')->where($map)->order('letter asc')->select();
        $letter = M('CarBrand')->where($map)->field('letter')->group('letter')->order('letter asc')->select();  
        
        $this->assign(array(
            'brandList' => $brandList,
            'letter' => $letter
        ));
        $this->display();
    }
    
    public function series($brand){
        
        $brandData = M('CarBrand')->where('id='.$brand)->find();
        
        $webCity = session('webcity');
        $webStore = session('webstore');
        $map = array();
        if($webCity){
            $siteCarSeries = S('SITE_CAR_SERIES_'.$webCity['id']);
            $map = array(
                'id' => array('in', implode(',', $siteCarSeries))
            ); 
        }
        if($webStore){
            $siteCarSeries = S('SITE_CAR_SERIES_STORE_'.$webStore['id']);
            $map = array(
                'id' => array('in', implode(',', $siteCarSeries))
            );            
        }
        $map['brand_id'] = $brand;
        $seriesList = D('CarSeriesView')->where($map)->select();
        $this->assign(array(
            'brandData' => $brandData,
            'seriesList' => $seriesList
        ));
        $this->display();
    }
}
