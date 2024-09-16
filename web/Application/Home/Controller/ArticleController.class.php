<?php
namespace Home\Controller;
use Think\AjaxPage;
use Think\Page;
/**
 * 咨询管理控制器
 */
class ArticleController extends HomeCommonController {

    public function index(){
        $Article_type = M('ArticleType');
        //非会员分类
        $Article_type_list = $Article_type
            ->where('is_member = 0')
            ->limit(2)
            ->order('sort asc')
            ->select();
        $this->assign('Article_type_list',$Article_type_list);
        $id = I('article_id');
        $type = I('type');
        $type_index = I('type_index');
        $first_articleType_id = $Article_type_list[0]['id'];
        $this->assign('first_articleType_id',$first_articleType_id);
        $this->assign('article_id',$id);
        $this->assign('type',$type);
        $this->assign('type_index',$type_index);
        if($id != ''){
            $count =  M('Article')->where('type_id='.$type.' and status = 1 ')->count();
            $Article = M('Article')->where('id='.$id)->select();
            $this->assign('Article_list',$Article);
            $Article_ids = M('Article')->field('id')->where('type_id='.$type.' and status = 1 ')->order('add_time desc')->select();
            $ids = i_array_column($Article_ids, 'id');
            $offset=array_search($id,$ids);
            if($offset >= 1){
                $upon_id = $ids[$offset-1];
                $upon_name = M('Article')->field('name,id')->where('id='.$upon_id)->find()['name'];
                $this->assign('upon_id',$upon_id);
                $this->assign('upon_name',$upon_name);
            }
            if($offset < $count){
                $next_id = $ids[$offset+1];
                $next_name = M('Article')->field('name,id')->where('id='.$next_id)->find()['name'];
                $this->assign('next_id',$next_id);
                $this->assign('next_name',$next_name);
            }
            $this->assign('d_type',$type);
        }
        $this->display();
    }

    public function article(){
        //分类
        $type = I('type');
        $type_index = I('type_index',0);
        $this->assign('type_index',$type_index);
        $where['type_id'] = $type;
        $where['a.status'] = array('eq',1);
        $count =  M('Article')->alias('a')->field('a.*,b.name type_name')
            ->join('ln_article_type  as b on a.type_id = b.id ','left')
            ->where($where)
            ->count();
        $page = new AjaxPage($count,6);
        $listRows = 6;
        $current_page = I('page',0);
        $firstRow = ($current_page-1)*$listRows;
        $show = $page->show();
        $Article_list = M('Article')
            ->where('type_id='.$type.' and status = 1')
            ->order("add_time desc")
            ->limit($firstRow.','.$listRows)
            ->select();
        $this->assign('type',$type);
        $this->assign('type_index',$type_index);
        $this->assign('current_page',$current_page);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('Article_list',$Article_list);
        $this->display();
    }

    public function detail(){
        $id = I('article_id');
        $type = I('type');
        $count =  M('Article')
            ->where('type_id='.$type.' and status = 1')
            ->count();
        $Article = M('Article')->where('id='.$id)->select();
        $this->assign('Article_list',$Article);
        $Article_ids = M('Article')->field('id')
            ->where('type_id='.$type.' and status = 1')
            ->order('add_time desc')->select();
        $ids = i_array_column($Article_ids, 'id');
        $offset=array_search($id,$ids);
        if($offset >= 1){
            $upon_id = $ids[$offset-1];
            $upon_name = M('Article')->field('name,id')->where('id='.$upon_id)->find()['name'];
            $this->assign('upon_id',$upon_id);
            $this->assign('upon_name',$upon_name);
        }
        if($offset < $count){
            $next_id = $ids[$offset+1];
            $next_name = M('Article')->field('name,id')->where('id='.$next_id)->find()['name'];
            $this->assign('next_id',$next_id);
            $this->assign('next_name',$next_name);
        }
        $this->assign('d_type',$type);
        $this->display();
    }

}