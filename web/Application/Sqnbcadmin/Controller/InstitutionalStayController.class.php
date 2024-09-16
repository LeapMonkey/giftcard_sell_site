<?php
namespace Sqnbcadmin\Controller;

/**
 * 机构入驻管理控制器
 * @author yuanyulin  <QQ:755687023>
 */
class InstitutionalStayController extends AdminCommonController {
    
    /**
     * 机构入驻显示显示控制器
     */
    public function index() {        
        $data = D("InstitutionalStay")->search();
        if($data) {
            $this->assign('InstitutionalStayData',$data['data']);
            $this->assign('page',$data['page']);
        }
        $this->display();
    }
    /**
     * 机构入驻增加和修改控制器
     * @param type $id 需要修改的机构人员id
     */
    public function edit($id = 0){
        $id = I('id', 0, 'intval');
        if (IS_POST) {
            $data = I('post.', '');
            if (D('InstitutionalStay')->create($data, 2) !== false) {
                if ($id > 0) {
                    D('InstitutionalStay')->where('id='. $id)->save($data);
                } else {
                    $this->ajaxReturn(V(0, '未知错误！'));
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, D('InstitutionalStay')->getError()));
            }
        } else {
            $InstitutionalStayRemark = M('InstitutionalStay')->where('id='. $id)->getfield('remark');
            $this->assign('remark', $InstitutionalStayRemark);
            $this->display();
        }
    }
    
    // 删除记录
    public function del(){
        $this->_del('InstitutionalStay');  //调用父类的方法
    }
}
