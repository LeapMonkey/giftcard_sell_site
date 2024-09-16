<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * 卡片专区管理控制器
 * @author zhengnian
 */
class CardModel extends Model{
    protected $insertFields = array('name','add_time','type_id','photo_path','sale_proportion','introduce','offline','moreintroduce','cardexample','openpassword','is_entitycard','card_length','card_password_length','card_logo','zdy_proportion');
    protected $updateFields = array('name','add_time','type_id','photo_path','sale_proportion','introduce','offline','offline','moreintroduce','cardexample','openpassword','is_entitycard','card_length','card_password_length','card_logo','zdy_proportion');
    protected $selectFields = array('id','name','add_time','type_id','photo_path','sale_proportion','introduce','offline','offline','moreintroduce','cardexample','openpassword','is_entitycard','card_length','card_password_length','card_logo','zdy_proportion');

    protected $_validate = array(
        array('name','require','标题内容不能为空', self::MUST_VALIDATE),
        array('name', '1,25', '标题名称不能超过25', self::MUST_VALIDATE, 'length', 3),
        array('type_id', 'number', '非法数据, 分类字段', self::MUST_VALIDATE, 'function', 3),
        array('type_id', '1,100000', '请选择卡片分类', self::MUST_VALIDATE, 'between', 3),
        array('sale_proportion','/^[1-9]\d*\.\d*|0\.\d*[1-9]\d*|0?\.0+|0$/','比例必须是小数。',3),

    );
    
    public function getInfo($map){
        $title = I('name', '','trim');
        if($title) {
            $map['a.name'] = array('eq',"$title");
        }
        $data = $this->getCardByPage($map, $field=null, $order='type_id asc');
        return $data;
    }
    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
          $data['cardexample'] = str_replace("\r\n", "<br>",$data['cardexample']);  

    }

     protected function _before_update(&$data,$options) {  
      
       $data['cardexample'] = str_replace("\r\n", "<br>",$data['cardexample']);    

    }  
  

    /**
     * 获取分页方法
     * @param array $where 传入where条件
     * @param string $order 排序方式
     * @return array 搜索数据和分页数据
     */
    public function getCardByPage($where, $field=null, $order='type_id asc ,add_time desc '){
        $keywords = I('name', '');

        if ($keywords) {
            $where['a.name'] = array('like','%'.$keywords.'%');
        }
        
        $count = $this->alias('a')->field('a.*,b.name type_name')
            ->join('ln_card_type  as b on a.type_id = b.id ','left')
            ->where($where)
            ->count();
        $page = get_page($count);
        $limit = $page['limit'];
        $list = $this->alias('a')->field('a.*,b.name type_name')
            ->join('ln_card_type  as b on a.type_id = b.id ','left')
            ->where($where)
            ->limit($limit)->order($order)->select();
        return array(
            'data' => $list,
            'page' => $page['page']
        );
    }

}
