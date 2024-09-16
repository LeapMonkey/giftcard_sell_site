<?php
namespace Common\Model;
use Think\Model;
/**
 * 后台用户管理
 * @author zhaojiping QQ:17620286 liniukeji.com
 *
 */
class UserModel extends Model{

	protected $_validate = array(
        array('username', '4,20', '用户名不正确, 请输入4到20位字符', self::MUST_VALIDATE, 'length', 4),
        array('password', '6,50', '登录密码长度不合法', self::MUST_VALIDATE, 'length', 4), //密码长度不合法, 只注册时验证
	);

    // 判断用户是否存在
    public function checkMemberExist($phone)
    {
        $where['phone'] = $phone;
        $where['status'] = 1;
        $count = $this->where($where)->count();
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    //app第三方登录
    public function wxlogin($openid, $photo_path, $nickname, $type)
    {
       
        if (!$openid) return V(0, '参数错误');
        if (!$photo_path) return V(0, '参数错误');
        if (!$nickname) return V(0, '参数错误');

        if ($type == 1) { //微信第三方登录
            $user = $this->field('wx_unionid,id,wx_usernick,qq_avatar_thumb,username,disabled,name,phone')->where(['wx_unionid' => $openid])->find();
        } else {
            $user = $this->field('qq_openid,id,qq_avatar_thumb,qq_usernick,username,disabled,name,phone')->where(['qq_openid' => $openid])->find();
        }
//       return $this->_sql();
        if (is_array($user)) {
            /* 验证用户密码 */
            if ($user['disabled'] != 0) {
                return V(0, '用户账号已经被禁用');
            }
            $this->updateLogin($user['id']); //更新用户登录信息
            $user['token'] = $this->_createTokenAndSave($user); //生成登录token并保存

            return V(1, '登录成功', $user);
        } else {
            $data['username'] = $nickname;
            $img = '/Uploads/Picture/' . randNumber(5) . time() . '.jpg';

            //$this->download($photo_path, '.'.$img);
           // $data['photo_path'] = $img;

            // $type == 1 ? $data['openid'] = $openid : $data['qqopenid'] = $openid;
            $data['add_time']=time();
            if ($type == 1) {
                $data['wx_unionid'] = $openid;
            } else {
                $data['qq_openid'] = $openid;
            }
            $id = $this->add($data);
            $user = $this->field('id,username,name,phone,token')->where(['id' => $id])->find();
            $this->updateLogin($user['id']); //更新用户登录信息
            $user['token'] = $this->_createTokenAndSave($user); //生成登录token并保存

            return V(1, '登录成功', $user);
        }

    }

    public function _createTokenAndSave($uid)
    {
        $token = time().randNumber(18);
        $where['id'] = $uid['id'];
        $data['token'] = $token;
        $model=M('User');
        $model->where($where)->data($data)->save();
        return $token;
    }


    /**
     * 更新用户登录信息
     * @param  integer $uid 用户ID
     */
    protected function updateLogin($uid)
    {
        $actionLog=M('ActionLog');
        $log['user_id']=$uid;
        $log['add_time']=time();
        $log['type']=1;
        $log['ip']=get_client_ip();
        $actionLog->add($log);
    }



    }
