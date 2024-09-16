<?php
namespace Sqnbcadmin\Controller;
/**
 * 后台车辆管理控制器
 * @author fudaqing  <QQ:1073874559>
 */
class AreaController extends AdminCommonController {
	/**
     * 区域列表 
     */
    public function index(){
    	$parent_id = I('parent_id',0);
    	if($parent_id == 0){
    		$parent = array('id'=>0,'name'=>"世界各国",'level'=>0);
    	}else{
    		$parent = M('area')->where("id=$parent_id")->find();
    	}
    	$area = M('area')->where("parent_id=$parent_id")->select();
    	$this->assign('parent',$parent);
    	$this->assign('area',$area);
    	$this->display();
    }   
    public function areaHandle(){
    	$data = I('post.');
    	$id = I('id');
    	$referurl =  isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U("Area/area");
    	if(empty($id)){
    		$data['level'] = $data['level']+1;
    		if(empty($data['name'])){
    			$this->error("请填写地区名称", $referurl);
    		}else{
    			$res = M('area')->where("parent_id = ".$data['parent_id']." and name='".$data['name']."'")->find();
    			if(empty($res)){
    				M('area')->add($data);
    				$this->success("操作成功", $referurl);
    			}else{
    				$this->error("该区域下已有该地区,请不要重复添加", $referurl);
    			}
    		}
    	}else{
    		M('area')->where("id=$id or parent_id=$id")->delete();
    		$this->success("操作成功", $referurl);
    	}
    }
}