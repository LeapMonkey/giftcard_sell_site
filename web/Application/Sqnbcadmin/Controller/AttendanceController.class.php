<?php
namespace Sqnbcadmin\Controller;

/**
 * 后台考勤记录控制器
 * @author liuyang  <QQ:594353482>
 */
class AttendanceController extends AdminCommonController {


    /**
     * 后台考勤记录列表
     */
    public function index(){
        //业务员姓名部门
        $name = I('get.name', '');
        if (!empty($name)) {
            //复合查询
            $twoparam = array();
            $twoparam['member.real_name'] = array('like', '%'.$name.'%');
            $twoparam['user_department.name']  = array('like','%'.$name.'%');
            $twoparam['_logic'] = 'or';
            $where['_complex'] = $twoparam;
        }
        //时间进行搜索
        $startTime = I('get.start_time', '', 'strtotime');
        $endTime = I('get.end_time', '', 'strtotime');
        if($startTime && $endTime){
            $where['attendance.sign_time'] = array('between',array($startTime,$endTime));
        }elseif($startTime){
            $where['attendance.sign_time'] = array('egt',$startTime);
        }elseif($endTime){
            $where['attendance.sign_time'] = array('elt',$endTime);
        }
        //类型
        $type = I('get.type', '');
        if ($type != '') {
            $where['attendance.type'] = array('eq', $type);
        }
        //异常考勤
        $delay = I('get.delay', '');
        if ($delay!=null) {
            //$map = ['attendance.delay_time'] = array('gt', 0);
            $map = '( attendance.delay_time > 0 or attendance.is_abnormal >0)';
        }
        //状态
        $where['attendance.status'] = array('eq', 0);
        $selectFields = 'attendance.id,attendance.type,attendance.sign_time,attendance.normal_time,attendance.delay_time,attendance.remark,member.real_name as member_name,user_department.name as dept_name,attendance.is_abnormal';
        $list = D('Admin/Attendance')->getAttendanceListByPage($where, $map, $selectFields, 'attendance.sign_time desc');
        $this->assign('list',$list['list']); 
        $this->assign('page',$list['page']);  
        $this->assign('delay',$delay);      
        $this->display();
    } 

    /**
     * 考勤记录逻辑删除
     */
    public function recycle(){
        $this->_recycle('Attendance');  //调用父类的方法
    }

}
