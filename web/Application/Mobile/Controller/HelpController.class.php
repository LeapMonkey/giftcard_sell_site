<?php
namespace Mobile\Controller;

/**
 * 帮助中心控制器
 */
class HelpController extends HomeCommonController {

    public function index(){


    $menu_list = M('Help')->field('id,title,content')->select();
    $this->assign('menu_list',$menu_list);



    $id = I('id',4,'intval');
    $aid = I('aid',0,'intval');
    $typeid = 0;
    //默认显示帮助中心的内容
    $detail = M('Help')->where('id='.$id)->find();
    if($aid != 0){
        $detail = M('Article')->where('id='.$aid)->find();
        $detail['title']=$detail['name'];
        $typeid =$detail['type_id'];

    }

    $cardtypelist = M('ArticleCategory')->field('id,name')->select();
    $article = M('Article');

    foreach ($cardtypelist as $k=>$v){
        $cardtypelist[$k][data] = $article->where('type_id='.$v['id'])->field('id,name')->select();

    }
    $this->assign('typeid',$typeid);
    $this->assign('id',$id);
    $this->assign('detail',$detail);
    $this->assign('cardtypelist',$cardtypelist);
    $this->assign('menu_list',$menu_list);

    $this->display();
}
    public function card(){
        $id = I('id',0,'intval');
         $detail = M('Help')->where('id='.$id)->find();

        $this->assign('detail',$detail);


        $this->display();
    }

    public function announcement(){
        $announcement=M('Announcement');
        $list=$announcement->order('id desc')->select();
        $this->assign('list',$list);
        $this->display('announcement');
    }
}