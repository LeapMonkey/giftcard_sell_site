<?php
namespace Sqnbcadmin\Controller;

/**
 * 用户管理控制器
 * @author zhengnian
 */
class UserController extends AdminCommonController {

    public function index() {        
        $data = D("User")->getInfo();

        if($data) {
            $this->assign('data',$data['data']);
            $this->assign('page',$data['page']);
        }
        $this->display();
    }

    public function realname() {
        $data = D("User")->getrealInfo();
       $type = I('type', '');
        $this->assign('type',$type);
        if($data) {
            $this->assign('data',$data['data']);
            $this->assign('page',$data['page']);
        }
        $this->display();
    }

/*if ($result && $balanceresult){
$this->success('审核成功!',U('SaleRecord/index'));
exit;
}else{

    $this->error('审核失败');
}*/


    public function permission($id = 0){
    $id = I('id', 0, 'intval');



    $data['is_permission'] = 1;

    $user = M('User')->where('id='.$id)->save($data);
    if($user !== false){

        $this->success('审核成功',U('User/realname',array('type'=>2)));
    }else {
        $this->error('审核失败');
    }
}
    public function nopermission(){

        $id = I('id', 0, 'intval');
        $data['is_permission'] = 0;
        $data['error_real_name']=I('remarks');

        $user = M('User')->where('id='.$id)->save($data);
        if($user !== false){
          //  $this->success('审核成功',U('User/realname',array('type'=>2)));
            $this->ajaxReturn(V(1,"审核成功"));
        }else {
            //$this->error('审核失败');
            $this->ajaxReturn(V(0,"审核失败"));
        }
    }

public function edit($id = 0){
        $id = I('id', 0, 'intval');
        if(IS_POST){
            $data['is_member'] = I('is_member');
            $data['is_permission'] = I('is_permission');
            $start_time = I('start_time');
            $end_time = I('end_time');
            $data['name'] = I('name');
            $data['start_time'] = strtotime($start_time);
            $data['end_time'] = strtotime($end_time);
            $user = M('User')->where('id='.$id)->save($data);
            if($user !== false){
                $this->ajaxReturn(V(1, '保存成功'));
                //$this->success('保存成功', 'User/index');
            }
        } else {
            $UserInfo = M('User')->field(true)->find($id);
            $this->assign('info', $UserInfo);
            $this->display();
        }
    }

    public function del(){
        $this->_del('User');
    }

      /**
     * 统计功能
     */
    public function getStatistics() {
        $start_time = I('start_time','2018-01-01');
        $end_time = I('end_time',date('y-m-d'));
        $map['start_time'] = $start_time;
        $map['end_time'] = $end_time;

        $memberRe = D('User')->statistics($map);

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

    //点击修改启用状态
    public function changeDisabled(){
        $id = I('id',0,'intval');
        $status = I('disabled',0,'intval');
        if($id>0){
            if($status==1){
                $data['disabled']=0;
            }else{
                $data['disabled']=1;
            }
            D('User')->where('id='.$id)->save($data);
            $this->ajaxReturn(V(1,"状态修改成功"));
        }else{
            $this->ajaxReturn(V(0,"未知错误"));
        }

    }
}

