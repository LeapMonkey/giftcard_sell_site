<?php
namespace Sqnbcadmin\Controller;

/**
 *  后台用户控制器
 * @author zhaojiping <QQ: 17620286>
 *
 */
class MemberController extends AdminCommonController {

    /**
     * 用户列表 mfy
     */
    public function userList(){
        $map['status']= array('neq',2);
        $result = D('Member')->getMemberByPage($map);

        //设置分页变量
        $this->assign('list', $result['list']);
        $this->assign('page', $result['page']);
        $this->display('index');
    }

    // 添加/修改用户 mfy
    public function edit(){
        $id = I('id', 0, 'intval');
        $country_code = C('COUNTRY_CODE');
        $this->country_code = $country_code;
        if (IS_POST) {
            $memberModel = D('Member');
            if ($memberModel->create() !== false) {
                if ($id > 0) {
                    if ($memberModel->where('id='. $id)->save() === false) {
                        $this->ajaxReturn(V(0, $memberModel->getError()));
                    }
                } else {
                    if ($memberModel->add() === false) {
                        $this->ajaxReturn(V(0, $memberModel->getError()));
                    }
                }
                $this->ajaxReturn(V(1,'保存成功!'));
            } else {
                $this->ajaxReturn(V(0, $memberModel->getError()));
            }
        } else {
            $member = M('Member');
            if ($id > 0) {
                $info = $member->field(true)->find($id);
                $this->info = $info;
            }
            $this->display();
        }
    }

    // 放入回收站 mfy
    public function recycle(){
        $id = I('id', 0);
        if ($id == 1) {
            $this->ajaxReturn(V(0, '超级管理员不能删除'));
        } else {
            $ids = explode(',', $id);
            if (in_array(1, $ids)) {
                $this->ajaxReturn(V(0, '超级管理员不能删除'));
            }
            $this->_recycle('Member');  //调用父类的方法
        }

    }

    /**
     * 回收站用户列表
     */
    public function recycleList(){
        $map['status'] = 2;
        $result = D('Member')->getMemberByPage($map);

        //设置分页变量
        $this->assign('list', $result['list']);
        $this->assign('page', $result['page']);
        $this->display('recycleList');
    }

    // 从回收站还原
    public function restore(){
        $this->_restore('Member');  //调用父类的方法
    }

    // 改变可用状态
    public function changeDisabled(){
        $id = I('id', 0);
        if ($id == 1 ) {
            $this->ajaxReturn(V(0, '超级管理员不能禁用'));
        } else {
            $this->_changeDisabled('Member');  //调用父类的方法
        }

    }

    // 删除图片
    public function delFile(){
        $this->_delFile();  //调用父类的方法
    }

    // 上传图片
    public function uploadImg(){
        $this->_uploadImg();  //调用父类的方法
    }

    /**
     * 跳转至选择人员的页面(多选)
     * @author liuyang 594353482@qq.com
    */
    public function toUserSelect(){
        //获取查询需要的数据
        $positionTree = D('Position')->getPositionTree();
        $userDepartmentTree = D('UserDepartment')->getUserDepartmentTree();
        $userRoleList = D('Role')->getAllRoleList();

        //设置查询需要的数据
        $this->assign('positionTree', $positionTree);
        $this->assign('userDepartmentTree', $userDepartmentTree);
        $this->assign('userRoleList', $userRoleList);

        $this->display('userSelect');
    }

    /**
     * 选择人员弹窗确认用户数据
     * 根据用户选择的按钮确认用户选择的人员数据
     * @author liuyang 594353482@qq.com
     */
    public function ajaxUserDataByButtonType(){
        $map['status'] = 0;
        $buttonType = I('get.buttonType', 0, 'intval');
        if ($buttonType==1) {
            //获取查询条件
            $department_id = I('get.department_id', 0, 'intval');
            $position_id = I('get.position_id', 0, 'intval');
            $role_id = I('get.role_id', 0, 'intval');
            if ($department_id!=null && $department_id!=0) {
                $map['member.department_id'] = array('in', $department_id.'');
            }
            if ($position_id!=null && $position_id!=0) {
                $map['member.position_id'] = array('in', $position_id.'');
            }
            if ($role_id!=null && $role_id!=0) {
                $map['member_role.role_id'] = array('in', $role_id.'');
            }
            //获取列表展示的用户数据
            $result = D('Member')->getMemberListNoPage($map, 'id,real_name');
        } else {
            $ids = I('get.ids', '');
            $map['member.id'] = array('in', $ids);
            $result = D('Member')->getMemberListNoPage($map, 'id,real_name');
        }
        $this->ajaxReturn($result);
    }

    /**
     * 根据部门获取用户数据
     * @author liuyang 594353482@qq.com
     */
    public function getUsersByDeptId(){
        $ids = I('ids','');
        $where['department_id'] = array('in' ,$ids);
        $where['status'] = 0;
        $field = 'id,real_name';
        $order = 'department_id desc,reg_time desc, id desc';
        $list = D('Member')->getMemberList($where ,$field ,$order);
        $this->ajaxReturn($list);
    }

    /**
     * 根据职务获取用户数据
     * @author liuyang 594353482@qq.com
     */
    public function getUsersByPostId(){
        $ids = I('ids','');
        $where['position_id'] = array('in' ,$ids);
        $where['status'] = 0;
        $field = 'id,real_name';
        $order = 'position_id desc,reg_time desc, id desc';
        $list = D('Member')->getMemberList($where ,$field ,$order);
        $this->ajaxReturn($list);
    }
    /**
     * ajax获取选择人员
     */
    public function ajaxMemberSelectData(){
        $map['status'] = 0;
        //获取查询条件
        $department_id = I('post.department_id', 0, 'intval');
        $position_id = I('post.position_id', 0, 'intval');
        $role_id = I('post.role_id', 0, 'intval');
        $keywords = I('post.keywords','');
        $page = trim(I('page', 1, 'intval'));
        if ($department_id!=null && $department_id!=0)
            $map['member.department_id'] = array('in', $department_id.'');
        if ($position_id!=null && $position_id!=0)
            $map['member.position_id'] = array('in', $position_id.'');
        if ($role_id!=null && $role_id!=0)
            $map['member_role.role_id'] = array('in', $role_id.'');
        //获取列表展示的用户数据
        $result = D('Member')->ajaxMemberPage($map,$page);
        $this->ajaxReturn($result);
    }

    /**
     * 修改用户个人信息
     */
    public function adminSetting(){
        if (IS_POST) {
            $rules = array(
                array('password', '6,12', '登录密码长度不合法', 2, 'length', 2), // 不为空时验证
            );
            $member = M("Admin");
            $data = $member->validate($rules)->create(I('post.'), 2);
            if (!$data){
                $this->ajaxReturn(V(0, $member->getError()));
            } else {
                if ($data['password'] == '') {
                    unset($data['password']);
                } else {
                    $data['password'] = $data['password'];
                }
                $saveData['password']   = $data['password'] ;
                $member->where('id='. UID)->save($saveData);
                $this->ajaxReturn(V(1,'保存成功! 设置的内容要在重新登录后才会生效'));
            }

        } else {
            $member = M('Admin')->field('id,nick_name')->where(array('id' => UID))->find();
            $this->info = $member;
            $this->display('adminSetting');
        }
    }



      /**
     * 统计功能
     */
    public function getStatistics() {
        $start_time = I('start_time','2018-01-01');
        $end_time = I('end_time',date('y-m-d'));
        $map['start_time'] = $start_time;
        $map['end_time'] = $end_time;

        $memberRe = D('member')->statistics($map);

        $order_data_lst = i_array_column($memberRe,'order_date');
        if(!empty($start_time)){
            $min_date = $start_time;
        }else{
            $min_date = min($order_data_lst);
        }

        $dates = getDateFromRange($min_date,date('y-m-d'));

        /***
         * 将数据转化为key value
         */
        $key_value_lst =[];
        foreach ($memberRe as $v){
            $key_value_lst[$v['order_date']]=$v['order_num'];
        }

        foreach ($dates as $day){
            $count_lst[] = $key_value_lst[$day]?:0;
            $date_lst[] = date('m/d', strtotime($day));
        }

        $order_lst = ['date_lst' => json_encode($date_lst), 'count_lst' => json_encode($count_lst)];

        $this->assign($order_lst);

        $this->display('statistics');

    }

}
