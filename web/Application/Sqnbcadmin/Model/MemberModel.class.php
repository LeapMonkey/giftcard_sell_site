<?php
namespace Sqnbcadmin\Model;
use Think\Model;
use Common\Tools\Emchat;
/**
 * 后台用户管理
 * @author mfy QQ:477751560 liniukeji.com
 *
 */
class MemberModel extends Model{
	protected $insertFields = array('login_type','open_id','supplier','username','email','phone','usernick','online_status','password','avatar','avatar_thumb','qr_code','address','country','langauge','longitude','latitude','push_channel','token','chat_username','chat_password','birthday','sex','status','reg_time','reg_ip','last_login_ip','last_login_time','favour_count');
	protected $updateFields = array('login_type','open_id','supplier','username','email','phone','usernick','online_status','avatar','avatar_thumb','qr_code','address','country','langauge','longitude','latitude','push_channel','token','chat_username','chat_password','birthday','sex','status','last_login_ip','last_login_time','favour_count');
	protected $selectFields = array('id','login_type','open_id','supplier','username','email','phone','usernick','online_status','password','avatar','avatar_thumb','qr_code','address','country','langauge','longitude','latitude','push_channel','token','chat_username','chat_password','birthday','sex','status','reg_time','reg_ip','last_login_ip','last_login_time','favour_count');

	protected $_validate = array(
		array('username', '4,20', '用户名不正确, 请输入4到20位字符', self::MUST_VALIDATE, 'length', 1),
		array('username', 'checkName', '用户名不正确, 请输入4到20位字符', self::MUST_VALIDATE, 'callback', 1),
		array('username', '', '用户名已被注册', self::MUST_VALIDATE, 'unique',1), //用户名被占用

		array('usernick', '1,30', '昵称长度有误', self::MUST_VALIDATE, 'length', 1),

		array('phone', 'isMobile', '手机号不是11位合法的手机号', self::MUST_VALIDATE, 'function', 3),
		array('phone', 'checkPhone','手机号码已被注册', self::MUST_VALIDATE, 'callback',3), //手机号被占用   */

		array('password', '6,41', '登录密码长度不合法', self::MUST_VALIDATE, 'length', 1), //密码长度不合法, 只注册时验证
		array('password', '6,41', '登录密码长度不合法', self::VALUE_VALIDATE, 'length', 2), // 不为空时验证

		array('email', 'isEmail', '邮箱地址不合法', self::VALUE_VALIDATE, 'function', 3), // 不为空时验证

		array('sex', array(0,1,2), '非法数据, 性别字段', self::EXISTS_VALIDATE, 'in', 3),
		array('birthday', 'validateDate', '用户生日不是一个合法的日期', self::VALUE_VALIDATE, 'function', 3),

		array('login_type', 'number', '非法数据1', self::VALUE_VALIDATE, 'function', 3),
		array('supplier', 'number', '非法数据2', self::VALUE_VALIDATE, 'function', 3),
		array('online_status', 'number', '非法数据3', self::VALUE_VALIDATE, 'function', 3),
		array('country', 'number', '非法数据4', self::VALUE_VALIDATE, 'function', 3),
		array('favour_count', 'number', '非法数据5', self::VALUE_VALIDATE, 'function', 3),

	);

	// 判断手机号码是否已经有了, 判断时不判断自已
	protected function checkPhone($data){
		$id = I('id', 0, 'intval');
		$where['phone'] = $data;
		// $where['status']= array('eq',0);
		if ($id > 0) {
			$where['id'] = array('neq', $id );
		}
		$count = $this->where($where)->count();
		if ($count > 0) {
			return false;
		}
		return true;
	}

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

	public function getMemberInfo($map, $field=null){
		if ($field == null) $field = $this->selectFields;

		$info = $this->where($map)->field($field)->find();

		if ($info) {
			$info['position_name'] = D('Position')->getPositionNameById($info['position_id']);
			$info['department_name'] = D('UserDepartment')->getUserDepartmentNameById($info['department_id']);
	        $info['photo_path_thumb_120'] = thumb($info['photo_path']);
	        $info['photo_path_thumb_220'] = thumb($info['photo_path'], 220, 220);
		}

		return $info;
	}

	// 获取用户列表(分页)
	public function getMemberByPage($map, $field=null, $order='reg_time desc, id desc'){
		if ($field == null) $field = $this->selectFields;

        $keywords = I('keywords', '');
        if ($keywords) {
       	 	$where = 'username like "%'. $keywords .'%" or usernick like "%'. $keywords .'%" or phone like "%'. $keywords .'%"';
        }
		$count = $this->where($where)->where($map)->count();
		//echo $this->_sql();die;
        $page = get_page($count);

        $list = $this->field($field)->where($where)->where($map)->limit($page['limit'])->order($order)->select();
        //echo $this->_sql();die;
        return array('list' => $list,'page' => $page);
	}

	/**
	 * 获取用户数据  不分页
	 * @author liuyang   594353482@qq.com
	 */
	public function getMemberList($where, $field=null, $order='reg_time desc, id desc'){
		if ($field == null) $field = $this->selectFields;
        $list = $this->field($field)->where($where)->order($order)->select();
        return $list;
	}

	protected function _before_insert(&$data, $option){

		$data['reg_time'] = time();
		$data['reg_ip'] = get_client_ip();

		$data['password'] = $data['password'];

		$data['login_type'] = 1;

		unset($data['id']);

		// 判断密码为空就不修改这个字段
		if(empty($data['birthday'])){
			unset($data['birthday']);
		}else{
			$data['birthday']= strtotime(I('birthday'));
		}

		// 生成环信账号
		$emchat_username = $this->_datetimeRand(); // 用户账号
		$emchat_password = randNumber(16);	// 用户密码
		$emchat = new Emchat();
        $result = $emchat->createUser($emchat_username,  $emchat_password); //创建环信用户
        if ($result['error'] != '') {
        	$this->error = '聊天账号创建失败';
            return false;
        }
		$data['chat_username'] = $emchat_username;
		$data['chat_password'] = $emchat_password;


	}

	protected function _after_insert(&$data,$option){

		$member_id = $data['id'];



	}

	protected function _before_update(&$data, $option){


		// 判断密码为空就不修改这个字段
		if(empty($data['password'])){
			unset($data['password']);
		} else{
			$data['password'] = md5($data['password']);
		}

		$data['birthday'] = I('post.birthday','');
		if(empty($data['birthday'])){
			unset($data['birthday']);
		}else{
			$data['birthday'] = strtotime($data['birthday']);
		}

		if ($data['id'] == 1) {
			unset($data['disabled']);
			unset($data['status']);
		}
		// 用户名不可以修改
		unset($data['username']);
		unset($data['id']);
	}

	public function getMemberNameById($id=0){
		$memberName = $this->where("id=$id")->getField('real_name');
		return $memberName;
	}

	/**
     * 统计功能
     */
    public function statistics($map){
        //获取订单总量
        $where['status']=0;

        if(!empty($map['start_time'])&&!empty($map['end_time'])){
            $where['reg_time']=['between',[strtotime($map['start_time']),strtotime($map['end_time'])]];
        }

        //按天统计
        $lst = $this->where($where)->group('FROM_UNIXTIME(reg_time,\'%Y-%m-%d\')')->field('count(1)   order_num,FROM_UNIXTIME(reg_time,\'%Y-%m-%d\')   order_date')->limit(60)->select();

        return  $lst;



    }
        
        //人员选择弹窗时使用
	public function ajaxMemberPage($map, $page, $field=null, $order='reg_time desc, id desc'){
            $page_size = C('PAGE_SIZE');
            if ($field == null) $field = $this->selectFields;
            $keywords = I('keywords', '');
            if ($keywords) {
                    $where = 'real_name like "%'. $keywords .'%" or username like "%'. $keywords .'%" or phone like "%'. $keywords .'%"';
            }
            $list_id = $this->distinct(true)->field('id')->alias('member')->where($where)->where($map)->join('__MEMBER_ROLE__ member_role on member_role.member_id=member.id','left')->select();
            $count = count($list_id);
            $list = $this->distinct(true)->alias('member')->field($field)
                    ->where($where)->where($map)
                    ->join('__MEMBER_ROLE__ member_role on member_role.member_id=member.id','left')
                    ->limit(($page-1)*$page_size, $page_size)
                    ->order($order)->select();
            foreach ($list as $key => $value) {
                $list[$key]['department_name'] = M('UserDepartment')->where("id=".$value['department_id'])->getfield('name');
            }
            $pages = getPageData($page, $count);
            return array('list' => $list,'page' => $pages);   
	}
	/**
	 * 生成日期与随机数字的字符串, 用下划线分隔
	 * @return String 日期_时间_毫秒微秒_4位随机数
	 * example 上传的文件名, 环信用户的用户名
	 */
	private function _datetimeRand(){
	    return date('Ymd_His') .'_'. rand(100000,999999);
	}
}
