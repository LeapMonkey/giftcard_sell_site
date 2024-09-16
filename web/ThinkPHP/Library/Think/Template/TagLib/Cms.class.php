<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/10
 * Time: 13:33
 */

namespace Think\Template\TagLib;
use Think\Template\TagLib;

/**
 * 自定义标签
 */

class Cms extends TagLib {
    protected $tags = array(
        'adv' => array('attr'=>'limit,sname,key,item','close'=>1),
        'msg' => array('attr'=>'limit,cat,type,img,order,key,item','close'=>1),
        'type' => array('attr'=>'limit,cat,order,key,item','close'=>1),
        'cms' => array('attr'=>'sql,key,item,result_name','close'=>1,'level'=>3), //sql 万能标签
        'tag' => array('attr'=>'name,key,item,order','close'=>1,'level'=>3),
        'tagMsg' => array('attr'=>'name,key,item,order','close'=>1,'level'=>3), //sql 万能标签
    );

    /**
     * 获取热门标签
     */
    public function _tag($tag,$content){
        $limit = !empty($tag['limit']) ? $tag['limit'] : '1';
        $key  =  !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
        $item  =  !empty($tag['item']) ? $tag['item'] : 'item';// 返回的变量item

       $tag_lst =  M("article")->alias('a')->where('display=0 and tag is not null and tag != ""')->order('click_count desc')->limit($limit)->select();

        $tagsArr=array();
        foreach($tag_lst as $v){
            $tags = $v['tag'];
            if(strpos($tags,',') > 0){
                $tagsArr=  array_merge($tagsArr,explode(',',$tags));
            }else{
                $tagsArr[]=$tags;
            }
        }
        $tagsArr=   array_unique($tagsArr);


        foreach($tagsArr as $v){
            $tagRe[] =array('name'=>$v,'htmlPath'=>U('Home/Article/tagList',array('tagName'=>$v)));
        }


        $listName= '_tag_arr_hot'.$limit;

        $$listName = array_slice($tagRe,0,$limit);
        $this->tpl->set($listName, $$listName);
        $parseStr   =   '<?php  foreach(  $'.$listName.'   as $'.$key.'=>$'.$item.'): ?>';
        $parseStr  .=   $this->tpl->parse($content);
        $parseStr  .=   '<?php endforeach; ?>';
        if(!empty($parseStr)) {
            return $parseStr;
        }else{
            return '';
        }
    }

    /**
     * 获取热门标签
     */
    public function _tagMag($tag,$content){
        $limit = !empty($tag['limit']) ? $tag['limit'] : '1';
        $key  =  !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
        $item  =  !empty($tag['item']) ? $tag['item'] : 'item';// 返回的变量item
        $tag_name  =  !empty($tag['name']) ? $tag['name'] : '0';// 返回的变量key

        $listName='_tag_msg_list'.$tag_name;
        $$listName = M("article")->alias('a')->where('tag like %'.$tag_name.'%')->order('sort desc')->limit($limit)->select();

        $this->tpl->set($listName, $$listName);
        $parseStr   =   '<?php  foreach(  $'.$listName.'   as $'.$key.'=>$'.$item.'): ?>';
        $parseStr  .=   $this->tpl->parse($content);
        $parseStr  .=   '<?php endforeach; ?>';


        if(!empty($parseStr)) {
            return $parseStr;
        }else{
            return '';
        }


    }



    /**
     * 广告标签
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
    public function _adv($tag,$content){
        $limit = !empty($tag['limit']) ? $tag['limit'] : '1';
        $key  =  !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
        $item  =  !empty($tag['item']) ? $tag['item'] : 'item';// 返回的变量item
        $simple_name  =  !empty($tag['sname']) ? $tag['sname'] : '0';// 返回的变量key
        $listName = 'lst'.$simple_name;
        $$listName = M("Advert")->alias('a')->join('__ADVERT_LOCATION__ al on a.type_id=al.id ')->
        where("simple_name ='".$simple_name."' and  display = 0")->field('a.*')->order('sort')->limit($limit)->select();
      
        $this->tpl->set($listName, $$listName);
        $parseStr   =   '<?php  foreach(  $'.$listName.'   as $'.$key.'=>$'.$item.'): ?>';
        $parseStr  .=   $this->tpl->parse($content);
        $parseStr  .=   '<?php endforeach; ?>';

        if(!empty($parseStr)) {
            return $parseStr;
        }else{
            return '';
        }

    }

    /**
     * 根据大类获取小类
     */
    public function _type($tag,$content){
        $limit = !empty($tag['limit']) ? $tag['limit'] : '1';
        $cat  =  !empty($tag['cat']) ? $tag['cat'] : '';// 返回的变量key
        $order  =  !empty($tag['order']) ? $tag['order'] : 'add_time desc';// 返回的变量img
        $key  =  !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
        $item  =  !empty($tag['item']) ? $tag['item'] : 'item';// 返回的变量item

        $lstname='_type_lst'.$cat;

        $where['c.simple_name']=$cat;
        $where['c.status']=0;

        $$lstname = M('article_type')->alias('a')->join('__ARTICLE_CATEGORY__ c on a.cat_id=c.id')->field('a.*')->where($where)->select();

        $this->tpl->set($lstname,$$lstname);
        $parseStr   =   '<?php  foreach( $'.$lstname.' as $'.$key.'=>$'.$item.'): ?>';
        $parseStr  .=   $this->tpl->parse($content);
        $parseStr  .=   '<?php endforeach; ?>';

        if(!empty($parseStr)) {
            return $parseStr;
        }else{
            return '';
        }


    }

    /**信息标签
     * @param $tag
     * @param $content
     * @return string
     */
    public function _msg($tag,$content){
        $limit = !empty($tag['limit']) ? $tag['limit'] : '1';
        $cat  =  !empty($tag['cat']) ? $tag['cat'] : '';// 返回的变量key
        $type  =  !empty($tag['type']) ? $tag['type'] : '';// 返回的变量type
        $img  =  !empty($tag['img']) ? $tag['img'] : '0';// 返回的变量img
        $order  =  !empty($tag['order']) ? $tag['order'] : 'add_time desc';// 返回的变量img
        $key  =  !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
        $item  =  !empty($tag['item']) ? $tag['item'] : 'item';// 返回的变量item

        $where['a.status']=0;
        $lstname='msg_lst'.$cat.$type;

        if(!$cat){
            $$lstname = M('article')->alias('a')->where($where)->order($order)->limit($limit)->select();
        }else if($type){
            $where['c.simple_name']=$cat;
            $where['t.simple_name']=$type;
            $$lstname = M('article')->alias('a')->join('__ARTICLE_TYPE__ t on a.type_id=t.id')->join('__ARTICLE_CATEGORY__ c on a.category_id=c.id')->field('a.*')->where($where)->order($order)->limit($limit)->select();
        }else{
            $where['c.simple_name']=$cat;
            $$lstname = M('article')->alias('a')->join('__ARTICLE_CATEGORY__ c on a.category_id=c.id')->field('a.*')->where($where)->order($order)->limit($limit)->select();

        }

        foreach($$lstname as $k=>&$v){
           $v['htmlPath'] = U('Home/Index/news_son',array('id'=>$v['id']));//yangchunfu wandou 专用
        }
        
        $this->tpl->set($lstname,$$lstname);
        $parseStr   =   '<?php  foreach( $'.$lstname.' as $'.$key.'=>$'.$item.'): ?>';
        $parseStr  .=   $this->tpl->parse($content);
        $parseStr  .=   '<?php endforeach; ?>';

        if(!empty($parseStr)) {
            return $parseStr;
        }else{
            return '';
        }
    }

    /**
     * sql 语句万能标签
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
    public function _cms($tag,$content){
        $sql = $tag['sql']; // sql 语句
        //  file_put_contents('a.html', $sql.PHP_EOL, FILE_APPEND);
        $sql = str_replace(' eq ', ' = ', $sql); // 等于
        $sql = str_replace(' neq  ', ' != ', $sql); // 不等于
        $sql = str_replace(' gt ', ' > ', $sql);// 大于
        $sql = str_replace(' egt ', ' >= ', $sql);// 大于等于
        $sql = str_replace(' lt ', ' < ', $sql);// 小于
        $sql = str_replace(' elt ', ' <= ', $sql);// 小于等于
        //$sql = str_replace(' heq ', ' == ', $sql);// 恒等于
        //$sql = str_replace(' nheq ', ' !== ', $sql);// 不恒等于

        // $sql = str_replace(')', '."', $sql);

        $key  =  !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
        $item  =  !empty($tag['item']) ? $tag['item'] : 'item';// 返回的变量item
        $result_name  =  !empty($tag['result_name']) ? $tag['result_name'] : 'result_name';// 返回的变量key

        //$Model = new \Think\Model();
        //$name = 'sql_result_'.$item.rand(10000000,99999999); // 数据库结果集返回命名
        $name = 'sql_result_'.$item;//.rand(10000000,99999999); // 数据库结果集返回命名
        //$this->tpl->tVar[$name] = $Model->query($sql); // 变量存储到模板里面去
        $parseStr   =   '<?php
                        $Model = new \Think\Model();
                         $'.$result_name.' = $'.$name.' = $Model->query("'.$sql.'");
                 ';
        $parseStr  .=   ' foreach($'.$name.' as $'.$key.'=>$'.$item.'): ?>';
        $parseStr  .=   $this->tpl->parse($content).$tag['level'];
        $parseStr  .=   '<?php endforeach; ?>';

        if(!empty($parseStr)) {
            return $parseStr;
        }
        return ;
    }
}
