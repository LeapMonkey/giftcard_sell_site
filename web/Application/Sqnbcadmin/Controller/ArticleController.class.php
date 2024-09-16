<?php
namespace Sqnbcadmin\Controller;

/**
 * 文章管理控制器
 * @author zhengnian 
 */
class ArticleController extends AdminCommonController {
   
    public function index() {
        $data = D("Article")->getInfo();
        if($data) {
            $this->assign('data',$data['list']);
            $this->assign('page',$data['page']);
        }
        $articleTypeId = I('type_id');
        $userId = UID;
        if($userId == 2){
            $where['id'] = array('in','110,111,115');
        }else if($userId == 3){
            $where['id'] = array('eq','118');
        }else if($userId == 4){
            $where['id'] = array('eq','116');
        }else if($userId == 5){
            $where['id'] = array('eq','117');
        }

        $article_type = M('ArticleCategory')->field(true)->where($where)->order('sort desc')->select();
        $this->assign('articleTypeId',$articleTypeId);
        $this->assign('article_type',$article_type);
        $this->display();
    }
  
    public function edit($id = 0){
        $id = I('id', 0, 'intval');
        if(IS_POST){
            $data = D('Article');
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
            $info = M('Article')->field(true)->find($id);
            if(false === $info){
                $this->error('信息错误');
            }
            $this->assign('info', $info);
            $userId = UID;
            if($userId == 2){
                $where['id'] = array('in','110,111,115');
            }else if($userId == 3){
                $where['id'] = array('eq','118');
            }else if($userId == 4){
                $where['id'] = array('eq','116');
            }else if($userId == 5){
                $where['id'] = array('eq','117');
            }
            $ArticleType = M('ArticleCategory')->where($where)->select();
            $this->assign('ArticleType', $ArticleType);
            $this->display();
        }
    }

    public function del(){
        $this->_recycle('Article');
    }

    public function uploadImg(){
        $this->_uploadImg();
    }
    
}