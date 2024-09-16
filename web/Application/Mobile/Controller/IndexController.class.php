<?php
namespace Mobile\Controller;
use Think\Controller;
/**
 * 前台首页控制器
 */
class IndexController extends HomeCommonController {

    //系统首页
    public function index(){
    	$res_login = is_login();
        $user_id = $res_login['id'];
    	$cardtype = M('CardType');

        $cardtype_info = $cardtype->select();
        $this->assign('cardtype_info',$cardtype_info);
        //公告
        $announcement=M('Announcement');
        $list=$announcement->order('id desc')->find();
        $this->assign('list',$list);
        //获得合作商
        $partner_list = get_partner();
        $this->assign('partner_list',$partner_list);
        $this->display();
    }

   
   
}