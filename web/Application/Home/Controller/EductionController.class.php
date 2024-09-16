<?php
namespace Home\Controller;
use Think\Controller;
use Think\AjaxPage;
use Think\Page;
/**
 * 教学专区控制器
 */
class EductionController extends HomeCommonController {

    public function index(){
        $Eduction_type = D('Admin/EductionType');
        $Eduction_type_list = $Eduction_type->getInfo(1);
        $this->assign('Eduction_type_list',$Eduction_type_list['data']);
        $first_eductionType_id = $Eduction_type_list['data'][0]['id'];
        $this->assign('first_eductionType_id',$first_eductionType_id);
        $id = I('eduction_id');
        $type = I('type');
        $this->assign('eduction_id',$id);
        $this->assign('type',$type);
        //股民学堂---type111
        $eduction = M('Eduction');
        $eduction_list = $eduction->where(array('type_id'=>111))
            ->order('add_time desc')
            ->select();
        $this->assign('eduction_list',$eduction_list);
        $this->assign('eduction_type',111);
        $eduction_live_list = $eduction->where(array('type_id'=>110))
            ->order('add_time desc')
            ->select();
        $this->assign('eduction_live_type',110);
        $type_name = M('EductionType')->where('id=111')->field('name')->find()['name'];
        $this->assign('type_name',$type_name);
        $this->assign('eduction_live_list',$eduction_live_list);
        if($id != ''){
            $count =  M('Eduction')->where('type_id='.$type)->count();
            $Eduction = M('Eduction')->where('id='.$id)->select();
            $this->assign('Eduction_list',$Eduction);
            $Eduction_ids = M('Eduction')->field('id')->where('type_id='.$type)->order('add_time desc')->select();
            $ids = i_array_column($Eduction_ids, 'id');
            $offset=array_search($id,$ids);
            if($offset >= 1){
                $upon_id = $ids[$offset-1];
                $upon_name = M('Eduction')->field('name,id')->where('id='.$upon_id)->find()['name'];
                $this->assign('upon_id',$upon_id);
                $this->assign('upon_name',$upon_name);
            }
            if($offset < $count){
                $next_id = $ids[$offset+1];
                $next_name = M('Eduction')->field('name,id')->where('id='.$next_id)->find()['name'];
                $this->assign('next_id',$next_id);
                $this->assign('next_name',$next_name);
            }
            $this->assign('d_type',$type);
        }
        $this->display();
    }

    public function eduction(){
        //分类
        $type = I('type');
        $type_index = I('type_index',0);
        $this->assign('type_index',$type_index);
        $where['type_id'] = $type;
        $count =  M('Eduction')->alias('a')->field('a.*,b.name type_name')
            ->join('ln_eduction_type  as b on a.type_id = b.id ','left')
            ->where($where)
            ->order('a.add_time desc')
            ->count();
        $page = new AjaxPage($count,6);
        $listRows = 6;
        $current_page = I('page',0);
        $firstRow = ($current_page-1)*$listRows;
        $show = $page->show();
        $Eduction_list = M('Eduction')
            ->where($where)
            ->order("add_time desc")
            ->limit($firstRow.','.$listRows)
            ->select();
        $this->assign('type',$type);
        $this->assign('type_index',$type_index);
        $this->assign('current_page',$current_page);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('Eduction_list',$Eduction_list);
        $this->display();
    }

    public function detail(){
        $id = I('eduction_id');
        $type = I('type');
        $count =  M('Eduction')->where('type_id='.$type)->count();
        $Eduction = M('Eduction')->where('id='.$id)->select();
        $this->assign('Eduction_list',$Eduction);
        $Eduction_ids = M('Eduction')->field('id')->where('type_id='.$type)->order('add_time desc')->select();
        $ids = i_array_column($Eduction_ids, 'id');
        $offset=array_search($id,$ids);
        if($offset >= 1){
            $upon_id = $ids[$offset-1];
            $upon_name = M('Eduction')->field('name,id')->where('id='.$upon_id)->find()['name'];
            $this->assign('upon_id',$upon_id);
            $this->assign('upon_name',$upon_name);
        }
        if($offset < $count){
            $next_id = $ids[$offset+1];
            $next_name = M('Eduction')->field('name,id')->where('id='.$next_id)->find()['name'];
            $this->assign('next_id',$next_id);
            $this->assign('next_name',$next_name);
        }
        $this->assign('d_type',$type);
        if($type == 110){
            $this->display('detailLive');
        }else{
            $this->display();
        }
    }

}