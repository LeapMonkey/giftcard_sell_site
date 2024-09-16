<?php

namespace Sqnbcadmin\Controller;
use Think\Controller;
/**
 * 后台基类
 */
class AdminCommonController extends Controller {

    /**
     * 后台控制器初始化
     */
    protected function _initialize(){
//$this->redirect('Public/login');
        // //记录当前用户id
         // define('UID', '');
          $ip=get_client_ip();
          //$r=$this->checkIP($ip);
//          if($r==-1){
//            $this->redirect('Public/login');
//          }
        define('UID', is_login()['uid']);
       
        // define('UID', 1);//暂时定义用户id为admin的id
        if( !UID ){// 还没登录 跳转到登录页面
            $this->redirect('Public/login');
        }

        /* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config = api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置
        //验证权限,防止用户跨界访问
    	$admin_id=C('USER_ADMINISTRATOR');
    	//非超级管理员情况下需要根据关联的用户组的权限判断权限
    	//下面代码暂时注释掉,当需要开放权限控制时释放即可
		$url = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        if (UID!=$admin_id && $url!='Sqnbcadmin/Index/index') {
            $res = $this->_checkPrivilege($url);
        } else {
            $res = array('status'=>1,'info'=>'允许防问!');
        }
        //$res = array('status'=>1,'info'=>'允许防问!');
        if(!$res['status']){//没有权限直接返回  status=0
            if(IS_AJAX){
                $this->ajaxReturn(v(0, $res['info']));
                exit;
            }else{
                $this->error($res['info']);
                exit;
            }
        }
        //记录操作日志
        $log['admin_id'] = UID;
        $log['module'] = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        $log['operate_time'] = time();
        $log['ip'] = get_client_ip();
        $log['admin_phone'] = M('Admin')->where('id='.UID)->getField('phone');
        if($log['module'] != "Admin/AdminLog/index"){
            M('AdminLog')->data($log)->add();
        }
    }
     function checkIP($ip) {
    //$ips = M('aa')->getField('ip', true);
    $ips = array('111.58.213.107','220.173.202.61','58.59.203.92');
    if (!in_array($ip, $ips)) {
        return -1;
    }
    return 1;
  }

	private function _checkPrivilege($url){
       $_lst = D('Menu')->getMenus(UID);

	   $where['id'] = array('in',$_lst);
	   $where['display'] = 1;

	   $where['url']=$url;

       $has =  M('Menu')->where($where)->count();

       if($has < 1){
           return array('status'=>0,'info'=>'您当前帐号没有此操作权限');
       }
       return array('status'=>1,'info'=>'允许防问!');
    }

    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     */
    protected function checkDynamic(){
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }
        return null;//不明,需checkRule
    }


    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     */
    final protected function accessControl(){
        if(IS_ROOT){
            return true;//管理员允许访问任何页面
        }
		$allow = C('ALLOW_VISIT');
		$deny  = C('DENY_VISIT');
		$check = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);
        if ( !empty($deny)  && in_array_case($check,$deny) ) {
            return false;//非超管禁止访问deny中的方法
        }
        if ( !empty($allow) && in_array_case($check,$allow) ) {
            return true;
        }
        return null;//需要检测节点权限
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
            $data['status'] = 2;
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
            $data['status'] = 1;
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

        $result = array( 'status' => 1, 'msg' => '上传完成');
        //判断有没有上传图片
        //p(trim($_FILES['photo2']['name']));
        if(trim($_FILES['photo']['name']) != ''){
            $upload = new \Think\Upload(C('PICTURE_UPLOAD')); // 实例化上传类
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
                    $filesnames = scandir($dir);
                    foreach ($filesnames as $key => $value) {
                        if ($value === '.' || $value === '..') {
                            continue;
                        }
                        $count = strpos($value, $oldImg.'_');
                        if ($count !== false) {
                            $file = '.' . __ROOT__ . C('UPLOAD_PICTURE_ROOT') . '/' . $info['savepath'].$value;
                            if (file_exists($file) == true) {
                                @unlink($file);
                            }
                        }
                    }
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

	/**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param array        $base    基本的查询条件
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     *
     * @return array|false
     * 返回数据集
     */
    protected function lists ($model,$where=array(),$order='',$base = array('status'=>array('egt',0)),$field=true){
        $options    =   array();
        $REQUEST    =   (array)I('request.');
        if(is_string($model)){
            $model  =   M($model);
        }

        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);

        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);

        $options['where'] = array_filter(array_merge( (array)$base, /*$REQUEST,*/ (array)$where ),function($val){
            if($val===''||$val===null){
                return false;
            }else{
                return true;
            }
        });
        if( empty($options['where'])){
            unset($options['where']);
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);

        return $model->field($field)->select();
    }
    /**
     * 覆盖上传附件
     */
    protected function _uploadField(){
        $oldImg = I('oldImg', '', 'htmlspecialchars');
        $savePath = I('savePath', '', 'htmlspecialchars');

        if($savePath != '') $savePath = $savePath . '/';

        $result = array( 'status' => 1, 'msg' => '上传完成');
        //判断有没有上传图片
        //p(trim($_FILES['photo2']['name']));
        if(trim($_FILES['photo']['name']) != ''){
            $upload = new \Think\Upload(C('FIELD_UPLOAD')); // 实例化上传类
            $upload->replace  = true; //覆盖
            $upload->savePath = $savePath; //定义上传目录
            $upload->exts = array('jpg','png','gif','jpeg','doc','docx','ppt','pptx','pps','xls','xlsx','pot','vsd','rtf','wps','et','dps','pdf','txt','mp3','3gp','wmv','avi','mp4'); //定义上传格式
            //如果有上传名, 用原来的名字
            if($oldImg != '') $upload->saveName = $oldImg;
            // 上传文件
            $info = $upload->uploadOne($_FILES['photo']);
            if(!$info) {
                $result = array( 'status' => 0, 'msg' => $upload->getError() );
            }else{
                $result['src'] = C('UPLOAD_FIELD_ROOT') . '/' . $info['savepath'] . $info['savename'];
                $result['file_name'] = $info['savename'];
            }
            $this->ajaxReturn($result);
        }
    }

}
