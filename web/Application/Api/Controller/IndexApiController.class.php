<?php

namespace Api\Controller;

/**
 * Copyright (c) 山东六牛网络科技有限公司 https://liuniukeji.com
 *
 * @Description
 * @Author         zhanglili  qq:1640054073
 * @Copyright      Copyright (c) 山东六牛网络科技有限公司 保留所有版权(https://www.liuniukeji.com)
 * @Date           2019.4.23
 * @CreateBy       PhpStorm
 */
class IndexApiController extends CommonApiController
{

    //首页
    public function index(){
        //卡券分类
        $cardtype = M('CardType');
        $cardtype_info = $cardtype->field('id,name,wapphoto_path')->select();
        foreach($cardtype_info as $k=>$v){
            if($v['wapphoto_path']){
                $cardtype_info[$k]['wapphoto_path']=MOBILE_URL.$v['wapphoto_path'];
            }
        }
        //公告
        $announcement=M('Announcement');
        $annoince=$announcement->order('id desc')->find();
        if($annoince){
            $annoince['add_time']=date('Y-m-d',$annoince['add_time']);
        }
        //我们能做什么
        $canModel=M('CanDo');
        $cando=$canModel->order('id asc')->select();
        foreach($cando as $c=>$v){
            if($v['photo_path']){
                $cando[$c]['photo_path']=MOBILE_URL.$v['photo_path'];
            }
           if($v['photo_blue']){
               $cando[$c]['photo_blue']=MOBILE_URL.$v['photo_blue'];
           }

        }
        $list['cando']=$cando;
        $list['cardType']=$cardtype_info;
        if($annoince==''){
            $annoince=array();
        }
        $list['annoince']=$annoince;
        $this->apiReturn(1, '详情', $list);
    }


    //公告列表
    public function announcement(){
        $announcement=M('Announcement');
        $list=$announcement->order('id desc')->select();
        foreach($list as $k=>$v){
            $list[$k]['year']=date('Y',$v['add_time']);
            $list[$k]['time']=date('m-d',$v['add_time']);
            $list[$k]['content']=htmlspecialchars_decode($v['content']);
        }
        $this->apiReturn(1, '公告列表', $list);
    }


    //转让协议等文章
    public function getNews(){
        $id=I('id');
        $detail = M('Help')->where('id='.$id)->find();
        $detail['content']=htmlspecialchars_decode($detail['content']);
        $this->apiReturn(1, '详情', $detail);
    }


    //关于我们
    public function aboutus(){
        $company =  M('Company')->find();
        $company['content']=htmlspecialchars_decode($company['content']);
        $this->apiReturn(1, '关于我们', $company);
    }



}

