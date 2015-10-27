<?php

namespace Home\Controller;
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
        $jssdk = new JSSDK("wx1151866347a84fcb", "bb430075f413abb20865537ebece2959");
        $signPackage = $jssdk->GetSignPackage();
        $this->assign('signPackage',$signPackage);
        
        
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



//微信方法
class JSSDK {
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode(file_get_contents("jsapi_ticket.json"));
    if ($data->expire_time < time()) {
      $accessToken = $this->getAccessToken();
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      $ticket = $res->ticket;
      if ($ticket) {
        $data->expire_time = time() + 7000;
        $data->jsapi_ticket = $ticket;
        $fp = fopen("jsapi_ticket.json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $ticket = $data->jsapi_ticket;
    }

    return $ticket;
  }

  private function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode(file_get_contents("access_token.json"));
    if ($data->expire_time < time()) {
      // 如果是企业号用以下URL获取access_token
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
      if ($access_token) {
        $data->expire_time = time() + 7000;
        $data->access_token = $access_token;
        $fp = fopen("access_token.json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $access_token = $data->access_token;
    }
    return $access_token;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }
}