<?php

namespace Mobile\Controller;
use Think\Controller;

class NewsController extends BaseController{
    
    public function index(){
        
        $modelNews = M('Article');
        $count = $modelNews->where($map)->count();
        $page  = new \Think\Page($count,5);
        $show  = $page->show();
        $newsList = $modelNews->where($map)->order('id desc, sort desc')->limit($page->firstRow.','.$page->listRows)->select();
       
        $this->assign(array(
            'newsList' => $newsList,
            'page' => $show
        ));
        $this->display();
    }
    
    public function _empty($id){
        $this->read($id);
    }


    protected function read($id){
        
        $newsData = M('Article')->where('id ='.$id)->find();
        
        $cookieUser = explode('-', cookie('user_news'));
        if(!in_array($id, $cookieUser)){
            M('Article')->where(array('id'=>$id))->setInc('clicktimes',1);
            cookie('user_news', $id.'-'.cookie('user_news'), 3600);
        }
        
        $this->assign(array(
            'newsData' => $newsData
        ));
        $this->display('read');
    }
}