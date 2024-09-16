<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 文章分类模型
 * @author yuanyulin QQ:755687023
 *
 */
class ArticleCategoryModel extends Model{
    protected $insertFields = array('name','simple_name','display','parent_id','sort');
    protected $updateFields = array('id','name','simple_name','display','parent_id','sort');
    protected $selectFields = array('id','name','simple_name','display','parent_id','sort','status');

    protected $_validate = array(
        array('name','require','文章分类内容不能为空', self::EXISTS_VALIDATE),
        array('name', '1,20', '文章分类名称不能超过20', self::EXISTS_VALIDATE, 'length', 3),
        array('name', 'checkName', '文章分类内容已经存在, 不能重复添加', self::EXISTS_VALIDATE, 'callback', 3),

        //array('ename', '1,200', '英文文章分类名称不能超过200', self::EXISTS_VALIDATE, 'length', 3),
        array('simple_name','require','文章分类简介不能为空', self::EXISTS_VALIDATE),
        array('simple_name', '1,20', '文章分类简介不能超过20', self::EXISTS_VALIDATE, 'length', 3),

        array('parent_id','number','上级分类必须选择', self::EXISTS_VALIDATE,'',3),
        array('sort','1,1000','排序必须填写', self::EXISTS_VALIDATE,'between',3),
    );

    protected function _before_insert(&$data,$option) {
    }
    protected function _before_update(&$data,$option) {
        $id = I('id',0,'intval');
        $parent_id = I('parent_id',0,'intval');
        $ids = getChildIds('ArticleCategory',$id);
        if(in_array($parent_id,$ids)){
                $this->error='上级分类不能选本身和子类！';
                return false;
        }
    }
    
    protected function checkName($data){
        $id = I('id', 0, 'intval');
        $where['name'] = $data;
        $where['status']= array('eq',0);
        if ($id > 0) {
            $where['id'] = array('neq', $id );
        }
        $count = $this->where($where)->count();
        if ($count > 0) {
            return false;
        }
        return true;
    }
    // 获取文章分类
    public function articleCategory($id){
        $list = $this->where(array('display' => 0, 'status' => 0))->field('id, name, parent_id')->order('sort')->select();
        $articleList = getCategoryTree($list, $id);
        return $articleList;
    }
    //获取树形列表
    public function getArticleCategoryTree($id){
    $articleCategoryData = $this->where(array('status'=>0))->order('sort')->field('id, name, parent_id')->select();
    $articleCategoryData = D('Common/tree')->toFormatTree($articleCategoryData);
    return $articleCategoryData;
    }
}
