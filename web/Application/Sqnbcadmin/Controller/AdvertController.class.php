<?php
namespace Sqnbcadmin\Controller;

/**
 * 广告管理控制器
 * @author yuanyulin  <QQ:755687023>
 */
class AdvertController extends AdminCommonController {
    
    /**
     * 广告显示控制器
     */
    public function index() {        
        $data = D("Advert")->search();
        if($data) {
            $this->assign('advertData',$data['data']);
            $this->assign('page',$data['page']);
        }
        $this->display();
    }
    /**
     * 广告增加和修改控制器
     * @param type $id 需要修改的广告的id
     */
    public function edit($id = 0){
//        var_dump($_POST);
        $id = I('id', 0, 'intval');
        if(IS_POST){
            $data = I('post.', '');
            if(D('Advert')->create($data) !== false){
                if ($id > 0) {                   
                    D('Advert')->where('id='. $id)->save();
                } else {
                    D('Advert')->add();
                }
                $this->ajaxReturn(V(1, '保存成功'));
            } else {
                $this->ajaxReturn(V(0, D('Advert')->getError()));
            }
        } else {
            /* 获取数据 */
            $advertInfo = M('Advert')->field(true)->find($id);
            if(false === $AdvertInfo) $this->error('广告信息错误');
            $advertLocationData = M("AdvertLocation")->select();
            
            $this->assign('advertInfo', $advertInfo);
            $this->assign('advertLocationData', $advertLocationData);
            $this->display();
        }
    }
    
    // 删除记录
    public function del(){
        $this->_del('Advert');  //调用父类的方法
    }

    // 删除图片
    public function delFile(){
        $this->_delFile();  //调用父类的方法
    }

    // 上传图片
    public function uploadImg(){
        $this->_uploadImg();  //调用父类的方法
    }
}
