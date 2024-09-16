<?php
namespace Sqnbcadmin\Controller;

/**
 * 产品类型管理控制器
 * @author zhengnian
 */
class ProductTypeController extends AdminCommonController {

    public function index() {        
        $data = D("ProductType")->getInfo();
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
            if(D('ProductType')->create($data) !== false){
                if ($id > 0) {                   
                    D('ProductType')->where('id='. $id)->save();
                } else {
                    D('ProductType')->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, D('ProductType')->getError()));
            }
        } else {
            $ProductTypeInfo = M('ProductType')->field(true)->find($id);
            $this->assign('info', $ProductTypeInfo);
            $this->display();
        }
    }

    public function del(){
        $this->_del('ProductType');
    }

}
