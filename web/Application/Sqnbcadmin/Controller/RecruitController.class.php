<?php
namespace Sqnbcadmin\Controller;

/**
 * 后台招聘信息控制器
 * @author yangchunfu <QQ:779733435>
 */
class RecruitController extends AdminCommonController {
    public function index(){
        $name = trim(I('name', ''));

        if(!empty($name)){
            $where['name'] = array('like', '%'.$name.'%');
        }

        $where['status'] = 0;

        $list = D('Recruit')->getRecruitByPage($where);

        $this->assign('list', $list);
        $this->display();
    }

    public function edit(){
      
        $id = I('id', 0, 'intval');

        $recruit = D('Recruit');
        if(IS_POST){
            if($recruit->create() !== false){
                if($id > 0){
                    if($recruit->save() !== false) {
                        $this->ajaxReturn(V(1, '保存成功'));
                    }
                } else {
                    if($recruit->add()){
                        $this->ajaxReturn(V(1, '保存成功'));
                    }
                }

                $this->ajaxReturn(V(0, '保存失败'));
            } else {
                $this->ajaxReturn(V(0, $recruit->getError()));
            }
        }

        $info = $recruit->getRecruitDetailById($id);

        //获取岗位类别
        $jobCategory = M('JobCategory')->where("status = 0")->select();
        
        $this->assign('jobCategory', $jobCategory);
        $this->assign('info', $info);

        $this->display();
    }

      // 删除记录
    public function del(){
        $this->_del('Recruit');  //调用父类的方法
    }

}
