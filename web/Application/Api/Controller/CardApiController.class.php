<?php

namespace Api\Controller;

/**
 * Copyright (c) 山东六牛网络科技有限公司 https://liuniukeji.com
 *
 * @Description    卡片信息管理
 * @Author         zhanglili  qq:1640054073
 * @Copyright      Copyright (c) 山东六牛网络科技有限公司 保留所有版权(https://www.liuniukeji.com)
 * @Date           2019.4.23
 * @CreateBy       PhpStorm
 */
class CardApiController extends CommonApiController
{
  //卡类列表
  public function getCardType(){
      $cardtype = M('CardType');
      $typeinfo = $cardtype->field('id,name,wapdropphoto_path')->select();
      foreach($typeinfo as $key=>$v){
          if($v['wapdropphoto_path']){
              $typeinfo[$key]['wapdropphoto_path']=MOBILE_URL.$v['wapdropphoto_path'];
          }else{
              $typeinfo[$key]['wapdropphoto_path']='';
          }
      }
      $this->apiReturn(1, '卡类列表', $typeinfo);
  }

  //卡种列表
    public function getCardList(){
        $typeid = I('typeid');
        $cardtype = M('CardType');
        $typeinfo = $cardtype->select();
        $cardtype_info = $cardtype->getField('id',true);
        if($typeid == ''){
            $where['type_id']= array('in',$cardtype_info);
        }else{
            $where['type_id']= $typeid;
        }
        $card = M('Card');
        $where['offline']= array('neq',1);
//        $where['type_id']= $typeinfo[0]['id'];
        $cardinfo = $card->field('id,name,card_logo,openpassword,photo_path,cardexample,introduce,sale_proportion,zdy_proportion,card_length,card_password_length,is_entitycard,offline,openpassword')->where($where)->select();
        foreach($cardinfo as $key=>$v){
            if($v['cardexample']){
                $cardinfo[$key]['cardexample']=htmlspecialchars_decode($v['cardexample']);
            }
            if($v['photo_path']){
                $cardinfo[$key]['photo_path']=MOBILE_URL.$v['photo_path'];
            }
            if($v['card_logo']){
                $cardinfo[$key]['card_logo']=MOBILE_URL.$v['card_logo'];
            }
        }
        $this->apiReturn(1, '卡类列表', $cardinfo);
    }

    //面值
    public function cardprices(){
        $id = I('id', 0, 'intval');//卡种
        $where['card_id'] = $id;
        $result_list = M('CardPrice')->where($where)->field('id,price,card_proportion,sale_price,price_name')->select();

        $card = M('Card');
        $wheredata['id']= $id;
        $sale_proportion = $card->where($wheredata)->field('sale_proportion,zdy_proportion')->find();
        if($result_list == ''){
            $this->apiReturn(0,'数据错误');
        }
        $arr['list'] = $result_list;
        $arr['discount'] =$sale_proportion['sale_proportion'];
        $arr['zdy_proportion'] =$sale_proportion['zdy_proportion'];
        $this->apiReturn(1,'面值',$arr);
    }

    //上传图片
    public function upImages(){

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =    $map['author'] = (1024*1024*10);// 设置附件上传大小 管理员10M  否则 3M
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
        $upload->replace  =     true; // 存在同名文件是否是覆盖，默认为false
        //$upload->saveName  =   'file_'.$id; // 存在同名文件是否是覆盖，默认为false
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->apiReturn(0, $upload->getError());
        }else{
            $comment_img = '/Uploads/'.$info['img_file']['savepath'].$info['img_file']['savename'];
            $this->apiReturn(1, '上传成功',$comment_img);
        }
    }



}

