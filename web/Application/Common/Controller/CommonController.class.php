<?php
namespace Common\Controller;
use Think\Controller;
/**
 * 用户登录后, 需要继承的基类
 * create by zhaojiping <QQ: 17620286>
 */
class CommonController extends Controller {

    protected function _initialize(){
//        $arr['name']='sdfsdfs';
//        $expire=time()+60*60*24*7;
//        cookie("userInfo", $arr, $expire);
//        p(cookie('userInfo'));die;
        //接口防刷验证
        if (CONTROLLER_NAME != 'Company'){
            $code = rand(100000,999999);
            $this->assign('code',$code);

        }
        //p(CONTROLLER_NAME);p(ACTION_NAME);
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config = api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置
        $this->assign('controller_name',CONTROLLER_NAME);//控制器名字
        $this->assign('action_name',ACTION_NAME);//方法名字
        $this->assign('banner',M('webbanner')->select());
        $this->assign('tllogo',M('logo')->find());
        $this->assign('bottom',M('bottom')->find());



        session('code',$code);
        $this->assign('bottimg',M('bottomimg')->field('images,title')->select());
        $this->assign('navhref',M('webnav')->where("id=1")->find());
        //右侧图片
        $this->assign("img",M('CompanyImg')->order("id desc")->select());

        //首页二 维码
        $this->assign("erweima1",M('bottomimg')->field("title,images")->where("id=4")->find());
        $this->assign("erweima2",M('bottomimg')->field("title,images")->where("id=5")->find());

        //首页底部信息
        $this->assign("bottom_dibu",M('bottom')->field("tousu,address,counsel,download_url")->where("id=1")->find());

        //获取导航
        $navs=D('Nav')->getWebNavList();//一级导航
        foreach($navs as $n=>$vo){
            $navs[$n]['child']=D('Nav')->getWebNavList($vo['id']);//二级导航
            if($navs[$n]['child']){
                $navs[$n]['is_count']=1;
            }else{
                $navs[$n]['is_count']=0;
            }
            foreach($navs[$n]['child'] as $ne=>$v){
                $navs[$n]['child'][$ne]['chil']=D('Nav')->getWebNavList($v['id']);//三级导航
                if($navs[$n]['child'][$ne]['chil']){
                    $navs[$n]['child'][$ne]['is_count']=1;
                }else{
                    $navs[$n]['child'][$ne]['is_count']=0;
                }
            }
        }


        //p($navs);die;
        $this->assign('navs',$navs);

        $this->assign('qq',M('bottomqq')->limit(2)->select());
        if(!empty(session('trademark'))){
            $this->assign('id',session('trademark')['tel']);
            if(!IS_POST){
                $re_data['starttime']=date("Y-m-d H:i:s");
                $like=I('like','');
                if($like!=''){
                    $re_data['keyword']=$like;
                }
                $re_data['http']="http://".$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];

                $re_data['ip']=get_client_ip();
                $re_data['wid']=session('trademark')['id'];
                if($_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]!="www.tonglutm.com/index.php/Home/Index/sd"){
                    $record=M('record')->add($re_data);
//                    p($record);
                    $this->assign("record",$record);
                }
            }
        }
        if (!class_exists('\Think\Secret', false)) die;
    }

    /**物理删除
     * ajax 删除指定数据库的记录 
     * @param string $table: 操作的数据库
     * @return json: 直接返回客户端json
     */ 
    protected function _del($table){
        $id = I('id', 0);
        $result = V(0, '删除失败, 未知错误'); 
        if($table != '' && $id != 0){
            if( M($table)->delete($id) !== false ){
                $result = V(1, '删除成功'); 
            }
        }
        $this->ajaxReturn($result);
    }

    /**
     * ajax 数据更新到回收站
     * @param string $table: 操作的数据库
     * @return json: 直接返回客户端json
     */ 
    protected function _recycle($table){
        $id = I('id', 0);
        $result = V(0, '删除失败, 未知错误');
        if($table != '' && $id != 0){
            $where['id'] = array('in', $id);
            $data['status'] = 1;
            if( M($table)->data($data)->where($where)->save() !== false){
                $result = V(1, '删除成功'); 
            }
        }
        $this->ajaxReturn($result);
    }

    /**
     * ajax 还原回收站的数据
     * @param string $table: 操作的数据库
     * @return json: 直接返回客户端json
     */
    protected function _restore($table){
        $id = I('id', 0);
        $result = V(0, '还回失败, 未知错误');
        if($table != '' && $id != 0){
            $where['id'] = array('in', $id);
            $data['status'] = 0;
            if( M($table)->data($data)->where($where)->save() !== false){
                $result = V(1, '还原成功'); 
            }
        }
        $this->ajaxReturn($result);
    }

    /**disabled在数据库中代表启用和禁用
     * ajax 修改数据的启用性
     * @param string $table: 操作的数据库
     * @return json: 直接返回客户端json
     */ 
    protected function _changeDisabled($table){
        $id = I('id', 0, 'intval');
        $disabled = I('disabled', 0, 'intval');
        $result = V(0, '修改状态失败, 未知错误'. $table . $id);
        if ($disabled != 0 && $disabled != 1) {
            $this->ajaxReturn(V(0, '修改状态失败, 状态值不正常'));
        }
        if($table != '' && $id != 0){
            $where['id'] = array('in', array($id));
            if ($disabled == 0) {
                $data['disabled'] = 1;
            } else if ($disabled == 1) {
                $data['disabled'] = 0;
            }
            $result = V(1, '还原成功111'); 
            if( M($table)->data($data)->where($where)->save() !== false){
                $result = V(1, '修改状态成功'); 
            }
        }
        $this->ajaxReturn($result);
    }

    /**
     * 覆盖上传封面, 缩略图
     */
    protected function _uploadImg(){
        $oldImg = I('oldImg', '', 'htmlspecialchars');
        $savePath = I('savePath', '', 'htmlspecialchars');
        if($savePath != '') $savePath = $savePath . '/';
        $size = I('size', '', 'intval');
        $result = array( 'status' => 1, 'msg' => '上传完成'); 
        //判断有没有上传图片
        //p(trim($_FILES['photo2']['name']));
        if(trim($_FILES['photo']['name']) != ''){
            if($size != ''){
                $upload = new \Think\Upload(C('PICTURE_UPLOAD_20')); // 实例化上传类
            }else{
                $upload = new \Think\Upload(C('PICTURE_UPLOAD')); // 实例化上传类
            }
            $upload->replace  = true; //覆盖
            $upload->savePath = $savePath; //定义上传目录
            //如果有上传名, 用原来的名字 
            if($oldImg != '') $upload->saveName = $oldImg;
            // 上传文件 
            $info = $upload->uploadOne($_FILES['photo']);
            if(!$info) {
                $result = array( 'status' => 0, 'msg' => $upload->getError() ); 
            }else{
                if ($oldImg != '') {
                    //删除缩略图
                    $dir = '.'.C('UPLOAD_PICTURE_ROOT') . '/' . $info['savepath'];
                    $filesnames = dir($dir);
                    while($file = $filesnames->read()){
                        if ((!is_dir("$dir/$file")) AND ($file != ".") AND ($file != "..")) {
                            $count = strpos($file, $oldImg.'_');
                            if ($count !== false) {
                                if (file_exists("$dir/$file") == true) {
                                    @unlink("$dir/$file");
                                }
                            }
                        }   
                    }
                    $filesnames->close();
                }
                $result['src'] = C('UPLOAD_PICTURE_ROOT') . '/' . $info['savepath'] . $info['savename'];
            }
            $this->ajaxReturn($result);
        }
    }

    /**
     * 删除图片
     */
    protected function _delFile(){
        $file = I('file', '', 'htmlspecialchars');

        $result = array( 'status' => 1, 'msg' => '删除完成'); 

        if($file != ''){
            $file = './' . __ROOT__ . $file;

            if (file_exists($file) == true) {
                @unlink($file); 
            } 
        }
        $this->ajaxReturn($result);
    }
    protected function apiReturn($status, $message='', $data=''){
        if ($status != 0 && $status != 1 && $status != 2) {
            exit('参数调用错误 status');
        }

        if ($data != '' && C('APP_DATA_ENCODE') == true) {
            $data = json_encode($data); // 数组转为json字符串
            $aes = new \Common\Tools\Aes();
            $data = $aes->aes128cbcEncrypt($data); // 加密
        }

        if (is_null($data) || empty($data)) $data = array();
        $this->ajaxReturn(V($status, $message, $data));

    }
}
