<?php
namespace Home\Controller;

/**
 * 前台首页控制器
 */
class DataInfoController extends HomeCommonController {
    //基本资料
    public function Index(){
        $user = M('User');
        $user_id =  UID;
        $user_info = $user->where('id='.$user_id)->find();
        $this->assign('user_info',$user_info);
        $this->display();
    }
    //实名认证
    public function nameAttest(){
        $user = M('User');
        $user_id =  UID;
        $user_info = $user->where('id='.$user_id)->find();
        $this->assign('user_info',$user_info);
       // if($user_info['is_permission']==1 || $user_info['is_permission']==1){
        if($user_info['phone']==''){
            $this->display();exit;
        }else{
            if($user_info['is_permission']==1 || $user_info['is_permission']==2){
                $this->display('real_name_yes');exit;
            }else{
                $this->display('real_name');exit;
            }

        }

    }

    //密码管理
    public function password(){
        $this->display();
    }
    //手机设置
    public function phone(){
        $user = M('User');
        $user_id =  UID;
        $user_info = $user->where('id='.$user_id)->find();
        $this->assign('user_info',$user_info);
        $this->display();
    }
    //邮箱设置
    public function  email(){
        $user = M('User');
        $user_id =  UID;
        $user_info = $user->where('id='.$user_id)->find();
        $this->assign('user_info',$user_info);
        $this->display();
    }
    //提现账号
    public function withdraw(){
        $bank_type=I('bank_type',1);
        $from_ac=I('from_action');
        $this->assign('from_action',$from_ac);
        $this->assign('bank_type',$bank_type);
        $user = M('User');
        $user_id =  UID;
        $user_info = $user->where('id='.$user_id)->find();

        $where['user_id']=UID;
        $accountModel=M('AccountNumber');
        //获取支付宝账号
        $zfinfo  =$accountModel->where(array('type'=>1))->where($where)->order('id desc')->select();
        if($zfinfo){
            foreach($zfinfo as $key=>$v){
                if(isMobile($v['cardnum'])===true){
                    $zfinfo[$key]['cardnum']=substr_replace($v['cardnum'],'*****',3,6);
                }
                if(isEmail($v['cardnum'])===true){
                    $arr = explode('@', $v['cardnum']);
                    $rest = substr($arr[0], 0, -2);
                    $arr[0] = str_replace($rest, str_repeat('*', strlen($rest)), $arr[0]);
                    $zfinfo[$key]['cardnum']=$arr[0].'@'.$arr[1];
                }
            }
        }
        //获取银行卡号
        $wxinfo  =$accountModel->where(array('type'=>2))->where($where)->order('id desc')->select();
        //p($accountModel->_sql());die;
        if($wxinfo){
            foreach($wxinfo as $key=>$v) {
                $wxinfo[$key]['cardnum'] = "尾号" . substr($v['cardnum'], -4);
            }
        }

        $this->assign('zfinfo',$zfinfo);
        $this->assign('wxinfo',$wxinfo);
        $this->assign('user_info',$user_info);
        $this->display();
    }


    //实名认证
    public function authentication(){
        $user_id = UID;
        $user = M('User');
        $append['name'] =I('real_name');
        $append['id_card'] = I('id_card');
        $append['id_card_phone_upon']=I('id_card_phone_upon');
        $append['id_card_phone_down']=I('id_card_phone_down');
        $append['id_card_phone_hold']=I('id_card_phone_hold');
        $append['is_permission'] = 2;
        //$result = $user->where('id='.$user_id)->save($append);
        $data = D('Home/User');
        if($data->create($append) !== false){
            if ($user_id > 0) {
               // $this->checkIdCard(I('real_name'),I('id_card'));//验证身份证号
                $r=$data->where('id='. $user_id)->save($append);

            }
            $this->ajaxReturn(V(1, '提交成功'));
        } else {
            $this->ajaxReturn(V(0, $data->getError()));
        }
    }
    //验证身份证号
    public function checkIdCard($name,$id_card){
        if($name && $id_card){
            //判断之前是否提交过
            $where_card['name']=$name;
            $where_card['id_card']=$id_card;
            $where_card['content']=array('neq','');
            $res=M('CardData')->where($where_card)->find();
            if($res){
                $result=$res['content'];
            }else{
                $result=$this->checkCard($name,$id_card);
                $add_data['name']=$name;
                $add_data['id_card']=$id_card;
                $add_data['add_time']=time();
                $add_data['content']=$result;
                M('CardData')->add($add_data);
            }
            $result=json_decode($result,true);
            if($result['error_code']==0){
                if($result['result']['isok']=='' || $result['result']['isok']==false){
                    $this->ajaxReturn(V(0, '身份证号与真实姓名不匹配'));
                }
            }else{
                $this->ajaxReturn(V(0, '认证中心库中无此身份证记录'));
            }
        }else{
            $this->ajaxReturn(V(0, '信息提交不完整'));
        }
    }

    //验证身份证号
    public function checkCard($name,$id_card){
        $host="http://aliyunverifyidcard.haoservice.com";
        $path="/idcard/VerifyIdcardv2";
        $querys="cardNo={$id_card}&realName={$name}";
        $appcode = C('Aliyun')['AppCode'];
        $get=getForm($host,$path,$appcode,$querys);
        return $get;
    }










}