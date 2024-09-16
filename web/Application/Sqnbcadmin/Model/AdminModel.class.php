<?php
namespace Sqnbcadmin\Model;

use Think\Model;

/**
 * 后台用户管理
 * @author zhaojiping QQ:17620286 liniukeji.com
 *
 */
class AdminModel extends Model {
    protected $insertFields = array('nick_name', 'user_name', 'email', 'phone', 'password', 'sex', 'status');
    protected $updateFields = array('id', 'nick_name', 'user_name', 'email', 'phone', 'password', 'sex', 'status','card_type');
    protected $selectFields = array('id', 'nick_name', 'user_name', 'email', 'phone', 'password', 'sex', 'status', 'reg_time', 'reg_ip', 'last_login_ip', 'last_login_time','card_type');
    protected $_validate = array(

        array('user_name', '4,20', '用户名不正确, 请输入4到20位字符', self::MUST_VALIDATE, 'length', 1),
        array('user_name', 'checkName', '用户名不正确, 请输入4到20位字符', self::MUST_VALIDATE, 'callback', 1),
        array('user_name', '', '用户名已被注册', self::MUST_VALIDATE, 'unique', 1), //用户名被占用

        array('phone', 'isMobile', '手机号不是11位合法的手机号', self::VALUE_VALIDATE, 'function', 3),
        // array('phone', '','手机号码已被注册', self::EXISTS_VALIDATE, 'unique',1), //手机号被占用   */
        array('phone', 'checkPhone', '手机号码已被注册', self::VALUE_VALIDATE, 'callback', 3), //手机号被占用   */

        array('password', '6,50', '登录密码长度不合法', self::MUST_VALIDATE, 'length', 1), //密码长度不合法, 只注册时验证
        array('password', '6,50', '登录密码长度不合法', self::VALUE_VALIDATE, 'length', 2), // 不为空时验证

        array('email', 'isEmail', '邮箱地址不合法', self::VALUE_VALIDATE, 'function', 3), // 不为空时验证

        array('status', array(0, 1), '非法数据, 用户是否启用', self::MUST_VALIDATE, 'in', 3),


    );


    // 判断手机号码是否已经有了, 判断时不判断自已
    protected function checkPhone($data) {
        $id = I('id', 0, 'intval');
        $where['phone'] = $data;
        // $where['status']= array('eq',0);
        if ($id > 0) {
            $where['id'] = array('neq', $id);
        }
        $count = $this->where($where)->count();
        if ($count > 0) {
            return false;
        }
        return true;
    }

    // 判断用户名首字符必须为字母, 且是字母或数字或字母数字的组合
    protected function checkName($data) {
        $firstCode = substr($data, 0, 1);
        if (ctype_alpha($firstCode)) {
            if (ctype_alnum($data)) {
                return true;
            }
        }
        return false;
    }


     function _before_insert(&$data, $option) {
        $data['password'] = md5($data['password']);
    }

    protected function _before_update(&$data, $option) {


        // 判断密码为空就不修改这个字段
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = md5($data['password']);
        }
        // 判断密码为空就不修改这个字段
        if ($data['id'] == 1) {

            unset($data['status']);

        }
        // 用户名不可以修改
        unset($data['user_name']);
        unset($data['id']);

    }

    public function getadminNameById($id = 0) {
        $adminName = $this->where("id=$id")->getField('nick_name');
        return $adminName;
    }

    /**
     * 用户登录认证
     * @param  string $user_name 用户名
     * @param  string $password 用户密码
     * @param  string $type 登录用户类型 admin||user
     * @return array
     */
    public function login($user_name = '', $password = '') {
        if ($user_name == '' || $password == '') {
            exit('参数错误!');
        }

        /* 获取用户数据 */
        $admin = $this->where('user_name="' . $user_name . '"')->find();

        // echo $this->_sql();die;
        //echo md5($password);echo '||||'; echo $admin['password'];
        if (is_array($admin)) {
            /* 验证用户密码 */

            if (md5($password) === $admin['password']) {

                if ($admin['status'] != 0) {
                    return V(0, '用户账号已经被禁用');
                }

                //登录成功，返回用户信息
                return V(1, '登录成功', $admin);
            } else {
                return V(0, '用户名或密码错误!');
            }
        } else {
            return V(0, '用户名或密码错误.');
        }
    }

    public function getAdminByPage($map, $order = 'reg_time desc, id desc') {


        $keywords = I('keywords', '');
        if ($keywords) {
            $where = 'user_name like "%' . $keywords . '%" or user_name like "%' . $keywords . '%" or phone like "%' . $keywords . '%"';
        }

        $count = $this->where($where)->where($map)->count();

        $page = get_page($count);

        $list = $this->where($where)->where($map)->limit($page['limit'])->order($order)->select();
        //echo $this->_sql();
        return array('list' => $list, 'page' => $page);
    }

    //查询登录用户的分组权限
    public function getAdminGroup(){
        $adminInfo=$this->alias('a')
            ->field('a.*,c.group_id')
            ->where(array('a.id'=>UID))
            ->join('__AUTH_GROUP_ACCESS__ c on c.uid=a.id','left')
            ->find();
        return $adminInfo;
    }

}
