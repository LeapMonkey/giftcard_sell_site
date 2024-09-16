<?php
namespace Sqnbcadmin\Controller;

/**
 *  后台用户控制器
 * @author zhaojiping <QQ: 17620286>
 *
 */
class AdminController extends AdminCommonController {

    /**
     * 管理员列表
     */
    public function index(){
        $result = D('Admin')->getAdminByPage();
       	//设置分页变量
       	$this->assign('list', $result['list']);
       	$this->assign('page', $result['page']);
        $this->display();
    }

    /**
     * 新增修改用户信息
     */
    public function edit(){
        $id = I('id', 0, 'intval');

        if (IS_POST) {
            $adminModel = D('Admin');
            if ($adminModel->create() !== false) {
                if ($id > 0) {
                    if ($adminModel->where('id='. $id)->save() === false) {
                        $this->ajaxReturn(V(0, $adminModel->getError()));
                    }
                } else {
                    if (($id = $adminModel->add()) === false) {
                        $this->ajaxReturn(V(0, $adminModel->getError()));
                    }
                }
                /**
                 * 保存用户组
                 */
                D('AuthGroup')->updateUserGroup($id,I('auth_group'));

                $this->ajaxReturn(V(1,'保存成功!'));
            } else {
                $this->ajaxReturn(V(0, $adminModel->getError()));
            }
        } else {
            $info = D('Admin')->find($id);
            $roleList = D('AuthGroup')->where('status=0')->field('id,title')->select();
            $tem_group_id = M('auth_group_access')->where('uid='.$id)->getField('group_id');
            //获取卡片分类,用来设置不同的权限
            $cardList=M('CardType')->select();
            $this->assign('cardList',$cardList);
            $this->assign('info',$info);
            $this->assign('tem_group_id',$tem_group_id);
            $this->assign('roleList',$roleList);
            $this->display();
        }
    }
    /**
     * 删除
     */
    public function del(){
        $id = I('id',0,'intval');
        if($id){
            $ids = explode(',', $id);
            if (in_array(1, $ids)) {
                $this->ajaxReturn(V(0, '超级管理员不能删除'));
            }
            $where['id']=array('in',$ids);
            M('admin')->where($where)->delete();
            $result = V(1, '删除成功');
        }else{
            $result = V(0, '删除失败');
        }
        $this->ajaxReturn($result);
    }
}
