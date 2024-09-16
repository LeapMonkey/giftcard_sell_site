<?php
namespace Sqnbcadmin\Controller;

/**
 * 产品服务管理控制器
 * @author zhengnian
 */
class ProductController extends AdminCommonController {

    public function index() {        
        $data = D("Product")->getInfo();
        
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
            if(D('Product')->create($data) !== false){
                if ($id > 0) {                   
                    D('Product')->where('id='. $id)->save();
                } else {
                    D('Product')->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, D('Product')->getError()));
            }
        } else {
            $ProductType = M('ProductType')->select();
            $this->assign('ProductType', $ProductType);
            $ProductInfo = M('Product')->field(true)->find($id);
            $file_path = $ProductInfo['attach_path'];
            if (!empty($file_path)) {
                $file_name = strrchr($file_path, '/');
                $ProductInfo['file_name'] = substr($file_name, 1);
            } else {
                $ProductInfo['file_name'] = '';
            }
            $this->assign('info', $ProductInfo);
            $this->display();
        }
    }

    public function del(){
        $this->_del('Product');
    }

    public function delFile(){
        $this->_delFile();
    }
    // 上传附件
    public function uploadField(){
        $this->_uploadField();  //调用父类的方法
    }

    public function uploadImg(){
        $this->_uploadImg();
    }
}
