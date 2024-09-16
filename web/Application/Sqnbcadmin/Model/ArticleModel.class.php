<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 *  文章列表模型
 *  @author zhengnian
 *
 */
class ArticleModel extends Model{
    protected $insertFields = array('name','content','introduce','type_id','add_time','photo_path','photo_path_mobile','source','sub_name','status');
    protected $updateFields = array('id','name','introduce','content','type_id','add_time','photo_path','photo_path_mobile','source','sub_name','status');
    protected $selectFields = array('id','name','introduce','content','type_id','add_time','photo_path','photo_path_mobile','source','sub_name','status');
    
    protected $_validate = array(
        array('name','require','文章标题内容不能为空', self::MUST_VALIDATE),
        array('name', '1,20', '文章标题不能超过20', self::MUST_VALIDATE, 'length', 3),
        array('introduce', '1,100', '简介不能超过100', self::VALUE_VALIDATE , 'length', 3),
        array('content','require','中文新闻简介内容不能为空', self::MUST_VALIDATE),

        array('type_id', 'number', '非法数据, 所属分类字段', self::MUST_VALIDATE, 'function', 3),
        array('type_id', '1,1000000', '请选择用户所属分类', self::MUST_VALIDATE, 'between', 3),
    );

    public function getInfo($type){
        $name = I('get.name', '','trim');
        $type_id = I('type_id');
        if($name) {
            $map['name'] = array('eq',"$name");
        }
        if($type_id){
            $map['type_id'] = $type_id;
        }
        $data = $this->getArticleByPage($map, $field=null, $order='type_id asc , add_time desc',$type);
        return $data;
    }
    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }
    
    public function getArticleByPage($where, $field=null, $order='type_id asc , add_time desc',$type){
        if ($field == null) {
            $field = $this->selectFields;
        }
        $where['a.status'] = array('eq',1);
        $userId = UID;
        if($userId == 2){
            $where['b.id '] = array('in','110,111,115');
        }else if($userId == 3){
            $where['b.id '] = array('eq','118');
        }else if($userId == 4){
            $where['b.id '] = array('eq','116');
        }else if($userId == 5){
            $where['b.id '] = array('eq','117');
        }
        $count = $this->alias('a')->field('a.*,b.name type_name')
            ->join('ln_article_type  as b on a.type_id = b.id ','left')
            ->where($where)
            ->count();
        $page = get_page($count);
        $limit = $page['limit'];
        if($type == 1){
            $limit = 4;
        }else if($type == 2){
            $limit = 5;
        }
        $list = $this->alias('a')->field('a.*,b.name type_name')
            ->join('ln_article_category  as b on a.type_id = b.id ','left')
            ->where($where)
            ->limit($limit)->order($order)->select();
        return array(
            'list' => $list,
            'page' => $page['page']
        );
    }
    
}
