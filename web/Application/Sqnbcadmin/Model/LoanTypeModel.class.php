<?php
namespace Sqnbcadmin\Model;

use Think\Model;

/**
 * 后台贷款类型管理
 * @author mfy liniukeji.com
 *
 */
class LoanTypeModel extends Model {

    protected $insertFields = array('name', 'sort', 'add_time','keywords','discription','title');
    protected $updateFields = array('name', 'sort', 'add_time','keywords','discription','title' );
    protected $selectFields = array('id', 'name', 'sort', 'add_time','keywords','discription','title' );

    protected $_validate = array(
        array('name','require','贷款类型名称不能为空', self::EXISTS_VALIDATE),
        array('name', '1,100', '贷款类型名称不正确, 请输入1到100位字符', self::MUST_VALIDATE, 'length', 3),
        array('sort','1,1000','排序必须填写', self::EXISTS_VALIDATE,'between',3),
        array('keywords','0,200','最多输入100个关键字', self::EXISTS_VALIDATE,'length',3),
        array('discription','0,1000','seo搜索描述内容过多', self::EXISTS_VALIDATE,'length',3),

    );

    protected function _before_insert(&$data, $option) {
        $data['add_time'] = NOW_TIME;
    }

    protected function _before_update(&$data, $option) {
        $data['add_time'] = NOW_TIME;
    }


    // 获取贷款分类信息
    public function getLoanTypeList($where, $order = 'sort asc,add_time desc') {

        $keywords = I('keywords', '');
        if ($keywords) {
            $where['name'] = array('like','%'.$keywords.'%');
        }

        $count = $this->where($where)->count();

        $page = get_page($count);

        $list = $this->where($where)->limit($page['limit'])->order($order)->select();
        //echo $this->_sql();
        return array('list' => $list, 'page' => $page);
    }

}
