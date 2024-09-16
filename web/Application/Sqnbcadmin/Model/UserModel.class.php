<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 用户管理控制器
 * @author zhengnian
 */
class UserModel extends Model{
    protected $insertFields = array('username','phone','password','is_member','add_time','is_permission','add_time','name','balance','disabled');
    protected $updateFields = array('username','phone','password','is_member','add_time','is_permission','add_time','name','balance','disabled');
    protected $selectFields = array('id','username','phone','password','is_member','add_time','is_permission','add_time','name','balance','disabled');

    protected $_validate = array(
        array('username', '4,20', '用户名不正确, 请输入4到20位字符', self::MUST_VALIDATE, 'length', 1),
        array('username', 'checkName', '用户名不正确, 请输入4到20位字符', self::MUST_VALIDATE, 'callback', 1),
        array('username', '', '用户名已被注册', self::MUST_VALIDATE, 'unique',1), //用户名被占用

        array('phone', 'isMobile', '手机号不是11位合法的手机号', self::MUST_VALIDATE, 'function', 3),
        array('phone', 'checkPhone','手机号码已被注册', self::MUST_VALIDATE, 'callback',3), //手机号被占用   */

        array('password', '6,12', '登录密码长度不合法', self::MUST_VALIDATE, 'length', 1), //密码长度不合法, 只注册时验证
        array('password', '6,12', '登录密码长度不合法', self::VALUE_VALIDATE, 'length', 2), // 不为空时验证

        array('qq','number','QQ号码不正确。'),

        array('is_member', array(0,1), '非法数据, 是否是会员字段', self::EXISTS_VALIDATE, 'in', 3),
    );

    // 判断用户名首字符必须为字母, 且是字母或数字或字母数字的组合
    protected function checkName($data){
        $firstCode = substr($data, 0, 1);
        if (ctype_alpha($firstCode)) {
            if (ctype_alnum($data)) {
                return true;
            }
        }
        return false;
    }

    // 获得媒体详情
    public function getInfo(){
        $keywords = I('keywords', '');
        if ($keywords) {
            //$where = 'phone like "%'. $keywords .'%"  or name like "%'. $keywords .'%"';
            $where['name'] = array('like',"%$keywords%");
        }
        $userid = I('userid', '','trim');
        if($userid) {
       
            $where['id'] = $userid;
        }
        $data = $this->getUserByPage($where, $field=null, $order='id desc');
        return $data;
    }
    // 获得实名
    public function getrealInfo(){
        $keywords = I('keywords', '');
        $type = I('type', '');
        if ($keywords) {
            //$where = 'phone like "%'. $keywords .'%"  or name like "%'. $keywords .'%"';
             $map['name'] = array('like',"%$keywords%");
        }
        $userid = I('userid', '','trim');
        if($userid) {
       
            $map['id'] = $userid;
        }
        if ($type == 2) {
            $where = 'is_permission='.$type;
        }else{
            $where = 'is_permission !=2';
        }
        $map['id_card'] = array('neq','');




        $count = $this->where($where)->where($map)->count();
        $page = get_page($count);
        $data = $this->where($where)->where($map)->limit($page['limit'])->order('id desc')->select();
        return array(
            'data' => $data,
            'page' => $page['page']
        );
    }
    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }

    protected function _before_update(&$data,$option) {
        $data['is_permission'] = 2;
    }

    /**
     * 获取分页方法
     * @param array $where 传入where条件
     * @param string $order 排序方式
     * @return array 搜索数据和分页数据
     */
    public function getUserByPage($where, $field=null, $order=' is_member desc,id asc'){

        if ($field == null) {
            $field = $this->selectFields;
        }
        $count = $this->where($where)->count();
        $page = get_page($count);
        $data = $this->field($field)->where($where)->limit($page['limit'])->order($order)->select();
        return array(
            'data' => $data,
            'page' => $page['page']
        );
    }

        /**
     * 统计功能
     */
    public function statistics($map){
        //获取订单总量
        $where['status']=0;

        if(!empty($map['start_time'])&&!empty($map['end_time'])){
            $where['add_time']=['between',[strtotime($map['start_time']),strtotime($map['end_time'])]];
        }

        //按天统计
       $lst = $this->where($where)->group('FROM_UNIXTIME(add_time,\'%Y-%m-%d\')')->field('count(1)   order_num,FROM_UNIXTIME(add_time,\'%Y-%m-%d\')   order_date')->limit(60)->select();
       // $lst = $this->where($where)->group('FROM_UNIXTIME(a_time,\'%Y-%m-%d\')')->field('count(1)   order_num,FROM_UNIXTIME(reg_time,\'%Y-%m-%d\')   order_date')->limit(60)->select();

        return  $lst;



    }

}
