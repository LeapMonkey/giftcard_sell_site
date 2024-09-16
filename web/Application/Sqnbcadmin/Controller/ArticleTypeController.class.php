<?php
namespace Sqnbcadmin\Controller;

/**
 * 类型管理控制器
 * @author zhengnian
 */
class ArticleTypeController extends AdminCommonController {

    public function index() {        
        $data = D("ArticleType")->getInfo();
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
            $data['is_member'] = I('is_member');
            if(D('ArticleType')->create($data) !== false){
                if ($id > 0) {                   
                    D('ArticleType')->where('id='. $id)->save();
                } else {
                    D('ArticleType')->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, D('ArticleType')->getError()));
            }
        } else {
            $ArticleTypeInfo = M('ArticleType')->field(true)->find($id);
            $this->assign('info', $ArticleTypeInfo);
            $this->display();
        }
    }

    public function del(){
        $this->_del('ArticleType');
    }

    public function delFile(){
        $this->_delFile();
    }

    public function uploadImg(){
        $this->_uploadImg();
    }

}
