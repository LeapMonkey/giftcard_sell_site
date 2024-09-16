<?php
namespace Sqnbcadmin\Controller;

/**
 * 协议管理控制器
 * @author zhengnian
 */
class ProtocolController extends AdminCommonController {

    public function index() {        
        $data = M("Protocol")->select();
        $this->assign('data',$data);
        $this->display();
    }

    public function edit($id = 0){
        $id = I('id', 0, 'intval');
        if(IS_POST){
            $data = I('post.', '');
            $data['add_time'] = time();
            if(D('Protocol')->create($data) !== false){
                if ($id > 0) {                   
                    D('Protocol')->where('id='. $id)->save();
                } else {
                    D('Protocol')->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, D('Protocol')->getError()));
            }
        } else {
            $ProtocolInfo = M('Protocol')->field(true)->find($id);
            $this->assign('info', $ProtocolInfo);
            $this->display();
        }
    }

    public function del(){
        $this->_del('Protocol');
    }


    public function uploadImg(){
        $this->_uploadImg();
    }
}
