<?php
namespace Sqnbcadmin\Controller;

/**
 * 文章分类控制器
 * @author 袁玉林 <755687023@qq.com>
 */
class ArticleCategoryController extends AdminCommonController {
    //文章分类列表
    public function index() {
        $userdepartment=D('ArticleCategory');
        $where['status'] = 0;
        $info=$userdepartment->field(true)->where($where)->order('sort, id')->select(); 
        
        //获取树形结构
        $data = D('Common/Tree')->toFormatTree($info);
        
        $this->assign('data',$data);
        $this->display();
    }
    //文章分类的添加与编辑
    public function edit($id = 0){
        $id = I('id', 0, 'intval');
        if(IS_POST){
            $department = D('ArticleCategory');
            if($department->create() !== false){
                if ($id > 0) {
                    if($department->where("id=".$id)->save()===false){
                        $this->ajaxReturn(V(0, $department->getError()));
                    }
                } else {
                    $department->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, $department->getError()));
            }
        } else if(!$id){
            $this->display();
        }else{
            $info = array();
            /* 获取数据 */
            $info = M('ArticleCategory')->field(true)->find($id);
            if(false === $info){
                $this->error('分类信息错误');
            }
            // 生成树型列表
            $ArticleCategoryTree = D('ArticleCategory')->getArticleCategoryTree($id);

            $this->assign('ArticleCategoryTree', $ArticleCategoryTree);
            $this->assign('info', $info);
            $this->display();
        }
    }
    // 放入回收站
    public function recycle(){
        $this->_recycle('ArticleCategory');  //调用父类的方法
    }

    // 2016-09-17 by wangzhiliang
    //查询文章分类列表
    public function selectArticle(){
        $userdepartment=D('ArticleCategory');
        $where['status'] = 0;
        $categoryList=$userdepartment->field(true)->where($where)->order('sort, id')->select();
        // 查询相关文章列表
        $where['display'] = 0;
        $id = I('id');
        if (!$id) {
            $id = $categoryList[0]['id'];
        }
        $keywords = I('post.keywords');
        if ($keywords) {
            $where['name'] = array('like', '%'. $keywords .'%'); 
        }
        $where['category_id'] = $id; 
        $field = 'id, name, add_time';
        $order = 'sort desc';
        $Article = D('Article');
        $articleList = $Article->getArticleByPage($where, $field, $order);

        $this->assign('keywords', $keywords);
        $this->assign('id', $id);
        $this->assign('articleList', $articleList['data']);
        $this->assign('page', $articleList['page']);
        $this->assign('categoryList', $categoryList);
        $this->display('Banner/article');
    }
}