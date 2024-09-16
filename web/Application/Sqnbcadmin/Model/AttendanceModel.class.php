<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 后台考勤记录
 * @author liuyang QQ:594353482
 *
 */
class AttendanceModel extends Model{
	
	protected $selectFields = array('id','type','sign_time','normal_time','delay_time','remark','is_abnormal');

	public function getAttendanceListByPage($where, $map, $selectedFields=null, $order='sign_time desc'){
		if ($selectedFields == null) {
			$selectedFields = $this->selectFields;
		}
        $count = $this->alias('attendance')
        		->join('__MEMBER__ member on member.id=attendance.member_id','left')
        		->join('__USER_DEPARTMENT__  user_department on member.department_id=user_department.id','left')
        		->where($where)
                ->where($map)
        		->count();

        $pageData = get_page($count);

        $list = $this->alias('attendance')
        		->join('__MEMBER__ member on member.id=attendance.member_id','left')
        		->join('__USER_DEPARTMENT__  user_department on member.department_id=user_department.id','left')
        		->field($selectedFields)
        		->where($where)
                ->where($map)
        		->limit($pageData['limit'])
        		->order($order)
        		->select();
        return array('list' => $list, 'page' => $pageData['page']);
    }
	
}
