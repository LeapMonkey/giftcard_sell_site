<?php
namespace Mobile\Controller;

/**
 * 积分兑换以及线下交易控制器
 */
class ExchangeController extends HomeCommonController {

    public function index(){
        //获得合作商
        $partner_list = get_partner();
        $this->assign('partner_list',$partner_list);
        $this->display();
    }

    public function unline(){
        //获得线下交易的卡片列表
         $card = M('Card');
        $where['offline']= array('eq',1);
        $cardinfo = $card->field('id,name,photo_path,sale_proportion,introduce')->where($where)->select();
        $this->assign('cardinfo',$cardinfo);
        $this->display();
    
    }

    public function recycle(){
        //获得线下交易的卡片列表
        $this->display();
    }
    //回收网点
    public function sites(){
        $levelInfo1 = M('Area')->where('level=1')->field()->select();
        $levelInfo2 = M('Area')->where('level=2')->field()->select();
        $levelInfo3 = M('Area')->where('level=3')->field()->select();
        $this->levelInfo1 = $levelInfo1;
        $this->levelInfo2 = $levelInfo2;
        $this->levelInfo3 = $levelInfo3;
        $this->display();
    }
    //网点详情
    public function sitesdetail(){
        $id=I("sitesid",0,intval);
        if($id <= 0){
            $this->ajaxReturn(V(0,'网络异常'));
        }
        $list=M("Network")->where("level3=".$id." and status !=2")->select();
        $this->assign("list",$list);
        $this->display();
    }
}