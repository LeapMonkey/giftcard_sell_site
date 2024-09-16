<?php
namespace Home\Controller;

/**
 * 产品服务控制器
 */
class ServiceController extends HomeCommonController {

    public function index(){
        //产品服务
        $type = I('type');
        $Product = D('Admin/Product');
        if($type){
            $map['b.id'] = $type;
        }else{
            $product_list = $Product->getInfo();
            $this->assign('product_list',$product_list['data']);
        }
        $article_day = M('Article')->where('type_id=110')->order('add_time desc')->select()[0];
        $this->assign('article_day',$article_day);
        $article_early = M('Article')->where('type_id=111')->order('add_time desc')->select()[0];
        $this->assign('article_early',$article_early);
        $article_early_list = M('Article')->where('type_id=111')->order('add_time desc')->select();
        $this->assign('article_early_list',$article_early_list);
        $this->display();
    }
}