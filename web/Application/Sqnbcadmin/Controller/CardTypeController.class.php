<?php
namespace Sqnbcadmin\Controller;

/**
 * 卡片类型管理控制器
 * @author zhengnian
 */
class CardTypeController extends AdminCommonController {

    public function index() {
        //针对不同卡类设置权限的
        $adminInfo=D('Admin')->getAdminGroup();
        if($adminInfo['group_id']==14){
            $where['type_id']=$adminInfo['card_type'];
        }
        if($adminInfo['group_id']==14){
            $where['id']=$adminInfo['card_type'];
        }
        $data = D("CardType")->getInfo($where);
//        p($data);die;
        if($data) {
            $this->assign('data',$data['data']);
            $this->assign('page',$data['page']);
        }

        $this->display();
    }
  
    public function edit($id = 0){
        $id = I('id', 0, 'intval');
        if(IS_POST){
            $data = I('post.', '');
   
            $data['add_time'] = time();
            if(D('CardType')->create($data) !== false){
                if ($id > 0) {                   
                    D('CardType')->where('id='. $id)->save();
                } else {
                    D('CardType')->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, D('CardType')->getError()));
            }
        } else {
            $userId = UID;
            if($userId == 3){
                $where['id'] = array('eq','110');
            }
            $EductionTypeInfo = M('CardType')->where($where)->field(true)->find($id);
            $this->assign('info', $EductionTypeInfo);
            $this->display();
        }
    }

    public function del(){
        $this->_del('CardType');
    }

    public function delFile(){
        $this->_delFile();
    }

    public function uploadImg(){
        $this->_uploadImg();
    }
}
