<?php
namespace Core\Controller;
use Think\Controller;
/**
 * 省市县三级联动控制器
 * @author liuyang <594353482@qq.com>
 */
class RegionController extends Controller {

    public function getDataByParentId() {
    	$regionList = D('Core/Region')->getRegionNameByParentId();

        $this->ajaxReturn($regionList);
    }

    //清空省市县缓存
	public function clearAllRegionData() {
    	for ($i = 0; $i < 6000; $i++) {
    		S('parent_id'.$i,null);
    	}
    	p("清理成功!");die;
    }

}