<?php
namespace Sqnbcadmin\Controller;

/**
 * 后台招聘信息控制器
 * @author yangchunfu <QQ:779733435>
 */
class RecruitImageTextController extends AdminCommonController {
    public function index(){
        $where['status'] = 0;

        $list = D('RecruitImageText')->getRecruitImageTextByPage($where);
    	
    	$this->assign('list', $list);
        $this->display();
    }

    public function edit(){
        $id = I('id', 0, 'intval');

        $recruitImageText = D('RecruitImageText');
        if(IS_POST){
            if($recruitImageText->create() !== false){
                if($id > 0){
                    if($recruitImageText->save() !== false) {
                        $this->ajaxReturn(V(1, '保存成功'));
                    }
                } else {
                    if($recruitImageText->add()){
                        $this->ajaxReturn(V(1, '保存成功'));
                    }
                }

                $this->ajaxReturn(V(0, '保存失败'));
            } else {
                $this->ajaxReturn(V(0, $recruitImageText->getError()));
            }
        }

        $info = $recruitImageText->getRecruitImageTextDetailById($id);
        $this->assign('info', $info);

        $this->display();
    }

      // 删除记录
    public function del(){
        $this->_del('RecruitImageText');  //调用父类的方法
    }

    // 上传图片
    public function uploadImg(){
        $this->_uploadImg();  //调用父类的方法
    }

     // 删除图片
    public function delFile(){

        $this->_delFile();  //调用父类的方法
    }

}
