<?php
namespace Sqnbcadmin\Controller;

/**
 *  后台用户控制器
 * @author zhaojiping <QQ: 17620286>
 *
 */
class AdminLogController extends AdminCommonController {

    public function index(){
        $keywords = I('keywords','');
        if($keywords){
            $where['a.admin_phone'] = array('like','%'.$keywords.'%');
            $where['b.user_name'] = array('like','%'.$keywords.'%');
            $where['_logic'] = "or";
            $map['_complex'] = $where;
        }
        $info = D('AdminLog')->getInfoByPage($map);
        $this->page = $info['page'];
        $this->info = $info['info'];
        $this->display();
    }

}
