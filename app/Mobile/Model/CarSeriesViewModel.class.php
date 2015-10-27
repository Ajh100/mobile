<?php
/*
国别试图
 */
namespace Mobile\Model;
use Think\Model\ViewModel;
class CarSeriesViewModel extends ViewModel{
	public $viewFields = array(
            'subbrand' => array('_table'=>'tc_car_sub_brand','title'=>'subtitle'),
            'series' => array('_table'=>'tc_car_series','id','title'=>'title','brand_id','pinyin','_on'=>'series.sub_bid=subbrand.id'),
	);
}