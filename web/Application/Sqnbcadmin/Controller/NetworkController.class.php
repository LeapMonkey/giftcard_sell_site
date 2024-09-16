<?php
namespace Sqnbcadmin\Controller;

/**
 * Copyright (c) 山东六牛网络科技有限公司 https://liuniukeji.com
 *
 * @Description
 * @Author         (zhanglili QQ:1640054073)
 * @Copyright      Copyright (c) 山东六牛网络科技有限公司 保留所有版权(https://www.liuniukeji.com)
 * @Date           2017/9/21 8:28
 * @CreateBy       PhpStorm
 */
class NetworkController extends AdminCommonController {


    /**
     * 网点列表 zhanglili
     */
    public function index(){

        $result = D('Network')->search();
        //p($result);die;
        //设置分页变量
        $this->assign('list', $result['data']);
        $this->assign('page', $result['page']);
        $this->display();
    }



    // 放入回收站 zhanglili
    public function recycle(){
        $id = I('id', 0);

            $ids = explode(',', $id);

            $this->_recycle('Network');  //调用父类的方

    }



    // 添加/修改网点 zhanglili
    public function edit(){
        $id = I('id', 0, 'intval');

        if (IS_POST) {
            $hotelModel = D('Network');
            if ($hotelModel->create() !== false) {
                if ($id > 0) {
                    if ($hotelModel->where('id='. $id)->save() === false) {
                        $this->ajaxReturn(V(0, $hotelModel->getError()));
                    }
                    $result =$id;
                } else {

                    $result =  $hotelModel->add();
                    if ($result === false) {
                        $this->ajaxReturn(V(0, $hotelModel->getError()));
                    }
                }
                $this->ajaxReturn(V(1,'保存成功!'));
            } else {
                $this->ajaxReturn(V(0, $hotelModel->getError()));
            }
        } else {
            $hotel = M('Network');
            if ($id > 0) {
                $info = $hotel->field(true)->find($id);
                $this->info = $info;
            }

            $area = D('area');
            $order['country']=$area->getNameById($info['country']);
            $order['state']=$area->getNameById($info['state']);
            $order['city']=$area->getNameById($info['city']);
            $this->assign('order',$order);
            $this->display();
        }
    }



    /*
     * 获取省级地区
     */
    public function getState(){
        $level = I('post.level');
        $data = M('Area')->where("level=$level")->select();

        $html = '';
        if($data){
            foreach($data as $h){
                $html .= "<option value='{$h['id']}'>{$h['name']}</option>";
            }
        }
        echo $html;
    }



    /*
     * 获取市级区
     */
    public function getCity(){
        $parent_id = I('post.parent_id');
        $data = M('Area')->where("parent_id=$parent_id")->select();

        $html = '';
        if($data){
            foreach($data as $h){
                $html .= "<option value='{$h['id']}'>{$h['name']}</option>";
            }
        }
        echo $html;
    }
    /**
     * 获取县级区
     */
    public function getCountry(){
        $parent_id = I('post.parent_id');
        $selected = I('post.selected',0);
        $data = M('Area')->where("parent_id=$parent_id")->select();

        $html = '';
        if($data){
            foreach($data as $h){
                $html .= "<option value='{$h['id']}'>{$h['name']}</option>";
            }
        }
        echo $html;
    }





    // 删除图片
    public function delFile(){
        $this->_delFile();  //调用父类的方法
    }

    // 上传图片
    public function uploadImg(){
        $this->_uploadImg();  //调用父类的方法
    }

      /**
     * webuploader 上传文件
     */
    public function ajax_upload(){
        // 根据自己的业务调整上传路径、允许的格式、文件大小
        ajax_upload('/Uploads/image/');
    }

    /**
     * webuploader 上传demo
     */
    public function webuploader(){
        // 如果是post提交则显示上传的文件 否则显示上传页面
        if(IS_POST){
            p($_POST);die;
        }else{
            $this->display();
        }
    }

//地图选点
    public function getMapPoint() {
        $lon = I('map_longitude','');
        $lat = I('map_latitude','');
        $don = I('longitude','');
        $dat = I('latitude','');
        $address = I('address','');
        $map_address = I('map_address','');
        if ($lon==0 && $lat==0) {
            $lon='118.362908';
            $lat='35.110661';
        }
        /*
        if ($lon==0 && $lat==0) {
            $getIp=get_client_ip();
            $content = file_get_contents('http://api.map.baidu.com/location/ip?ak=TVbGGqSVvTCXfrIXzR604gvFeD7sag2Q&ip='.$getIp.'&coor=bd09ll');
            $json = json_decode($content);
            $map_longitude=$json->{'content'}->{'point'}->{'x'};
            $map_latitude=$json->{'content'}->{'point'}->{'y'};
            if (empty($map_longitude)) {
                $lon='118.362908';
                $lat='35.110661';
            } else {
                $lon=$map_longitude;
                $lat=$map_latitude;
            }
        }

        $content = file_get_contents('http://api.map.baidu.com/geocoder/v2/?location='.$lat.','.$lon.'&output=json&ak=TVbGGqSVvTCXfrIXzR604gvFeD7sag2Q');
        $json = json_decode($content);
            $city=$json->{'result'}->{'addressComponent'}->{'city'};*/
            if (empty($city)) {
                    $city='临沂市';
            }
            $this->assign('city',$city);
            $this->assign('lon',$lon);
            $this->assign('lat',$lat);
            $this->assign('don',$don);
            $this->assign('dat',$dat);
            $this->assign('address',$address);
            $this->assign('map_address',$map_address);
            $this->display ('select_map_point');
    }

}
