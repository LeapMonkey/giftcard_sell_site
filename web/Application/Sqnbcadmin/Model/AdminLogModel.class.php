<?php
namespace Sqnbcadmin\Model;

use Think\Model;

/**
 * 后台用户管理
 * @author zhaojiping QQ:17620286 liniukeji.com
 *
 */
class AdminLogModel extends Model {
    Public function getInfoByPage($where){
        $count = $this->count();
        $pageData = get_page($count);
        $info = $this->alias('a')->field('a.*,b.user_name as admin_name')
        ->join('ln_admin as b on a.admin_id = b.id','left')
        ->where($where)
        ->order('a.operate_time desc')->limit($pageData['limit'])->select();
        return array(
                'page'=>$pageData['page'],
                'info'=>$info,
            );
    }

}
