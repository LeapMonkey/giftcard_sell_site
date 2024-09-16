<?php
namespace Sqnbcadmin\Controller;
/**
 * Copyright (c) 山东六牛网络科技有限公司 https://liuniukeji.com
 *
 * @Description
 * @Author         (zhanglili QQ:1640054073)
 * @Copyright      Copyright (c) 山东六牛网络科技有限公司 保留所有版权(https://www.liuniukeji.com)
 * @Date           2017/9/21 8:28
 * @CreateBy       PhpStorm
 */
class AnnouncementController extends AdminCommonController {
   
    public function index() {
        $data = D("Announcement")->getInfo();
        if($data) {
            $this->assign('data',$data['list']);
            $this->assign('page',$data['page']);
        }
        $this->display();
    }
  
    public function edit($id = 0){
        $id = I('id', 0, 'intval');
        if(IS_POST){
            $data = D('Announcement');
            if($data->create() !== false){
                if ($id > 0) {
                    $data->where('id='. $id)->save();
                } else {
                    $data->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, $data->getError()));
            }
        } else {
            $info = array();
            $info = M('Announcement')->field(true)->find($id);
            if(false === $info){
                $this->error('信息错误');
            }
            $this->assign('info', $info);
            $this->display();
        }
    }

    public function del(){
        $this->_del('Announcement');
    }

    public function uploadImg(){
        $this->_uploadImg();
    }
    
}