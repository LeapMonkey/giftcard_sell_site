<?php
namespace Home\Controller;

/**
 * 帮助中心控制器
 */
class HelpController extends HomeCommonController {

    public function index(){
        $id = I('id','','intval');
        $aid = I('aid',0,'intval');
        $typeid = 0;
        //默认显示帮助中心的内容
        if($id){
            $detail = M('Help')->where('id='.$id)->find();
        }else{
          
            $where_help['static']=array('neq',2);
            $detail = M('Help')->where($where_help)->order('id desc')->find();
        }

        //平台公告
        if($id==11){
            $announcement=M('Announcement');
            $list=$announcement->order('id desc')->limit(30)->select();
            $this->assign('list',$list);
        }
        if($aid != 0){
            $detail = M('Article')->where('id='.$aid)->find();
            $detail['title']=$detail['name'];
            $typeid =$detail['type_id'];

        }
        $map['static']  = array('neq',2);
        $menu_list = M('Help')->field('id,title')->where($map)->select();
        $map_article['status']  = array('neq',2);
        $cardtypelist = M('ArticleCategory')->field('id,name')->where($map_article)->select();
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

    public function announcement(){

    }


    
}