<?php
namespace Admin\Model;
use Think\Model;
/**
 * 用户管理控制器
 * @author zhengnian
 */
class UserModel extends Model{
    protected $insertFields = array('username','phone','qq','password','is_member','add_time');
    protected $updateFields = array('username','phone','qq','password','is_member','add_time');
    protected $selectFields = array('id','username','phone','qq','password','is_member','add_time');

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
    protected function checkPhone($data){
        $id = I('id', 0, 'intval');
        $where['phone'] = $data;
        if ($id > 0) {
            $where['id'] = array('neq', $id );
        }
        $count = $this->where($where)->count();
        if ($count > 0) {
            return false;
        }
        return true;
    }
    // 获得媒体详情
    public function getInfo(){
        $phone = I('phone', '','trim');
        if($phone) {
            $map['phone'] = array('like',"%$phone%");
        }
        $data = $this->getUserByPage($map, $field=null, $order='add_time desc, is_member desc');
        return $data;
    }
    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }

    /**
     * 获取分页方法
     * @param array $where 传入where条件
     * @param string $order 排序方式
     * @return array 搜索数据和分页数据
     */
    public function getUserByPage($where, $field=null, $order='add_time desc, is_member desc'){

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

}
