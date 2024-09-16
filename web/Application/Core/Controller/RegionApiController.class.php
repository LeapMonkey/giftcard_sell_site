<?php
namespace Core\Controller;
/**
 * 客户管理API
 * create by yangchunfu <QQ:779733435>
 */
class RegionApiController extends \Common\Controller\CommonApiController {
    
    public function getRegionList(){
        $regionList = D('Core/Region')->field('id,region_name,parent_id')->select();

        $this->apiReturn(1, '区域列表', $regionList);
    }
}