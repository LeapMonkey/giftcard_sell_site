<?php
namespace Home\Controller;

/**
 * 关于我们控制器
 */
class AboutController extends HomeCommonController {

    public function index(){
        $company =  M('Company')->find();
        $this->assign('company',$company);
        $this->display();
    }
}