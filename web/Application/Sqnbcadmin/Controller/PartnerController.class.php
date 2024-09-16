<?php
namespace Sqnbcadmin\Controller;

/**
 * 管理控制器合作
 * @author zhengnian
 */
class PartnerController extends AdminCommonController {

    public function index() {        
        $data = D("Partner")->getInfo();
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
            if(D('Partner')->create($data) !== false){
                if ($id > 0) {                   
                    D('Partner')->where('id='. $id)->save();
                } else {
                    D('Partner')->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, D('Partner')->getError()));
            }
        } else {
            $LinkInfo = M('Partner')->field(true)->find($id);
            $this->assign('info', $LinkInfo);
            $this->display();
        }
    }

    public function del(){
        $this->_del('Partner');
    }

    public function uploadImg(){
        $this->_uploadImg();
    }
}
