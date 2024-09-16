<?php
namespace Sqnbcadmin\Controller;

/**
 * 帮助管理控制器
 * @author zhengnian
 */
class HelpController extends AdminCommonController {

    public function index() {        
        $data = D("Help")->getInfo();
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
            if(D('Help')->create($data) !== false){
                if ($id > 0) {                   
                    D('Help')->where('id='. $id)->save();
                } else {
                    D('Help')->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, D('Help')->getError()));
            }
        } else {
            $TeamInfo = M('Help')->field(true)->find($id);
            $this->assign('info', $TeamInfo);
            $this->display();
        }
    }

    public function del(){
        $this->_del('Help');
    }

    public function delFile(){
        $this->_delFile();
    }

    public function uploadImg(){
        $this->_uploadImg();
    }
}
