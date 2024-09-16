<?php
namespace Sqnbcadmin\Controller;

/**
 * 后台角色控制器
 * @author liuyang <QQ:594353482>
 */
class RoleController extends AdminCommonController {
    public function index(){
        $name = I('name','');
        if($name){
            $where['name'] = array('like','%'.$name.'%');
        }
        $where['status']=0;
        $info = M('Role')->where($where)->field(true)->order('id,sort')->select();
        $this->assign('info',$info);
        $this->display();
    }

    public function edit($id=0){
        $id = I('id',0,'intval');
        
        if(IS_POST){
            $role = D('Role');
            $data = $role->create();
            if($data){
                if($id>0){
                    $role->where("id=$id")->save($data);
                }else{
                    $role->add($data);
                }
                $this->ajaxReturn(V(1,"保存成功"));
            }else{
                $this->ajaxReturn(V(0,$this->getError()));
            }
        }else{
            $info = M('role')->field('id,status,remark,sort,name')->where("id=$id")->find();
            $this->assign('info',$info);
            $this->display();
        }

    }

    //点击修改启用状态
    public function changeDisabled(){
       $id = I('id',0,'intval');
       $status = I('status',0,'intval');
       if($id>0){
            if($status==1){
                $data['status']=0;      
            }else{
                $data['status']=1;
            }
           D('Role')->where('id='.$id)->save($data);
           $this->ajaxReturn(V(1,"状态修改成功"));
       }else{
        $this->ajaxReturn(V(0,"未知错误"));
       }
       
    }

    public function recycle(){
        $this->_recycle('Role');
    }

    /**
     * 角色树数据获取(用于在弹窗选择人员时获取左侧的角色树展示)
     * @author liuyang 594353482@qq.com
     */
    public function getRoleTreeData(){
        $roleModel = M('Role');
        $where['status'] = 0;
        $info = $roleModel->where($where)->field('id,name')->order('sort,id')->select();
        //$Tree = new \Common\Tools\BuildTreeArray($info,'id','parent_id','0');
        //$data = $Tree->getTreeArray();
        $this->ajaxReturn($info);
    }

}
