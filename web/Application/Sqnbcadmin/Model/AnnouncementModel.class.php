<?php
namespace Sqnbcadmin\Model;
use Think\Model;
/**
 * Copyright (c) 山东六牛网络科技有限公司 https://liuniukeji.com
 *
 * @Description
 * @Author         (zhanglili QQ:1640054073)
 * @Copyright      Copyright (c) 山东六牛网络科技有限公司 保留所有版权(https://www.liuniukeji.com)
 * @Date           2017/9/21 8:28
 * @CreateBy       PhpStorm
 */
class AnnouncementModel extends Model{
    protected $insertFields = array('name','content','add_time');
    protected $updateFields = array('id','name','content','add_time');
    protected $selectFields = array('id','name','content','add_time');
    
    protected $_validate = array(
        array('name','require','文章标题内容不能为空', self::MUST_VALIDATE),
        array('name', '1,10', '文章标题不能超过10', self::MUST_VALIDATE, 'length', 3),
        array('content','require','公告内容不能为空', self::MUST_VALIDATE),
    );

    public function getInfo(){
        $name = I('get.name', '','trim');
        if($name) {
            $map['name'] = array('eq',"$name");
        }

        $data = $this->getArticleByPage($map, $field=null, $order='id desc , add_time desc');
        return $data;
    }
    protected function _before_insert(&$data,$option) {
        $data['add_time'] = time();
    }
    
    public function getArticleByPage($where, $field=null, $order='type_id asc , add_time desc'){
        if ($field == null) {
            $field = $this->selectFields;
        }

        $count = $this->where($where)->order($order)->count();
        $page = get_page($count);
        $limit = $page['limit'];

        $list = $this->where($where)->limit($limit)->order($order)->select();
        return array(
            'list' => $list,
            'page' => $page['page']
        );
    }
    
}
