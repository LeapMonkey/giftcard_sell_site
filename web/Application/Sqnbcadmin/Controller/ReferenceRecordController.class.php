<?php
namespace Sqnbcadmin\Controller;

/**
 * 针股记录管理控制器
 * @author zhengnian
 */
class ReferenceRecordController extends AdminCommonController {

    public function index() {        
        $data = D("ReferenceRecord")->getInfo();
        $is_notice = I('is_notice','','trim');
        $this->assign('is_notice', $is_notice);
        if($data) {
            $this->assign('data',$data['data']);
            $this->assign('page',$data['page']);
        }
        $this->display();
    }

    public function edit($id = 0){
        $id = I('id', 0, 'intval');
        if(IS_POST){
            $data['is_notice'] = I('is_notice');
            $user = M('ReferenceRecord')->where('id='.$id)->save($data);
            if($user !== false){
                $this->ajaxReturn(V(1, '保存成功'));
            }
        } else {
            $ReferenceRecordInfo = M('ReferenceRecord')->field(true)->find($id);
            $this->assign('info', $ReferenceRecordInfo);
            $this->display();
        }
    }

    public function del(){
        $this->_del('ReferenceRecord');
    }
}
