<?php
namespace Sqnbcadmin\Controller;

/**
 *
 * @author zhengnian
 */
class CompanyController extends AdminCommonController {

    public function index(){
        $data = D("Company")->getInfo();
        if($data) {
            $this->assign('list',$data['list']);
            $this->assign('page',$data['page']);
        }

        $this->display();
    }
    
    public function edit($id = 0){

        $id = I('id', 0, 'intval');
        if(IS_POST){
            $Company = D('Company');
            if($Company->create() !== false){
                if ($id > 0) {
                    $Company->where('id='. $id)->save();
                } else {
                    $Company->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, $Company->getError()));
            }
        } else {
            /* 获取数据 */
            $info = M('Company')->field(true)->find($id);
            $this->assign('info', $info);
            $this->display();

        }
    }
    // 删除记录
    public function recycle(){
        $this->_del('Company');
    }

    public function getCompanyNameById($id){
        $name = $this->where("id=$id")->getField('name');
        return $name;
    }
    
}
