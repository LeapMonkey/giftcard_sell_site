<?php
namespace Sqnbcadmin\Controller;

/**
 * 岗位类别控制器
 * @author yangchunfu <QQ:779733435>
 */
class JobCategoryController extends AdminCommonController {
    public function index(){
       $where['status'] = 0;

       $list = M('JobCategory')->where($where)->select();

       $this->assign('list', $list);
       $this->display();
    }

    public function edit(){
        $id = I('id', 0, 'intval');
        $jobCategory = D('JobCategory');
        if(IS_POST){
            $name = I('post.name', '');
            $ename = I('post.ename', '');

            if(empty($name) || strlen($name) > 255){
                $this->ajaxReturn(V(0, '中文岗位名称有误'));
            }

            if(empty($ename) || strlen($ename) > 255){
                $this->ajaxReturn(V(0, '英文岗位名称有误'));
            }

            $sort = I('post.sort', 0, 'intval');
            if(!is_numeric($sort)){
                $this->ajaxReturn(V(0, '排序必须为数字'));
            }

            $data['name'] = $name;
            $data['ename'] = $ename;
            $data['sort'] = $sort;
            if($id > 0){
                if($jobCategory->where("id=$id")->save($data) !== false) {
                    $this->ajaxReturn(V(1, '保存成功'));
                }
            } else {
                if($jobCategory->add($data)){
                    $this->ajaxReturn(V(1, '保存成功'));
                }
            }

            $this->ajaxReturn(V(0, '保存失败'));
        }

        //获取岗位类别
        $info = M('JobCategory')->where("id=$id")->find();

        $this->assign('info', $info);
        $this->display();
    }

      // 删除记录
    public function del(){
        $d_id = I('id', 0, 'intval');

        $count = M('Recruit')->where("category_id=$d_id")->count();

        if($count>0){
            $this->ajaxReturn(V(0, '有招聘信息在使用此岗位，请先删除招聘信息'));
        }

        $this->_del('JobCategory');  //调用父类的方法
    }


}
