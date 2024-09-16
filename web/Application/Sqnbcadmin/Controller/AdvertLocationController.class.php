<?php
namespace Sqnbcadmin\Controller;

/**
 * 广告位置控制器
 * @author yuanyulin  <QQ:755687023>
 */
class AdvertLocationController extends AdminCommonController {
    /**
     * 广告位置控制器
     */
    public function index() {        
        $data = D("AdvertLocation")->search();
        if($data) {
            $this->assign('advertLocationData',$data['data']);
            $this->assign('page',$data['page']);
        }
        $this->display();
    }
    /**
     * 广告位置增加和修改控制器
     * @param type $id 需要修改的广告位置的id
     */
    public function edit($id = 0){
        $id = I('id', 0, 'intval');
        if(IS_POST){
            if(D('AdvertLocation')->create($data) !== false){
                if ($id > 0) {                   
                    D('AdvertLocation')->where('id='. $id)->save();
                } else {
                    D('AdvertLocation')->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, D('AdvertLocation')->getError()));
            }
        } else {
            /* 获取数据 */
            $advertInfo = M('AdvertLocation')->field(true)->find($id);
            if(false === $AdvertLocationInfo) $this->error('广告位置信息错误');
            
            $this->assign('advertLocationInfo', $advertInfo);
            $this->display();
        }
    }
    
    // 删除记录
    public function del(){
        $this->_del('AdvertLocation');  //调用父类的方法
    }

}
