<?php

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(

    /* SESSION 和 COOKIE 配置 */
    //'SESSION_PREFIX' => 'ln_home', //session前缀
    'PAGE_SIZE' => 10,
    /* 后台错误页面模板 */
    'TMPL_ACTION_ERROR' => MODULE_PATH . 'View/Public/info.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => MODULE_PATH . 'View/Public/success.html', // 默认成功跳转对应的模板文件

    //开启多语言
    'LANG_SWITCH_ON' => true, 
    'LANG_AUTO_DETECT' => true,
    'LANG_LIST'        => 'zh-cn,en-us',
    'DEFAULT_LANG' => 'zh-cn',
    'VAR_LANGUAGE'     => 'l',

);
