<?php

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
    'DEFAULT_MODULE'     => 'Home',
    'MODULE_DENY_LIST'   => array('Common'),
    //'MODULE_ALLOW_LIST'  => array('Home','Admin'),
     'THINK_SDK_QQ'      => array(
        'APP_KEY'       => '111111', //应用注册成功后分配的 APP ID
        'APP_SECRET'    => 'xxxxxxxxxxxxxx', //应用注册成功后分配的KEY
        'CALLBACK'      => URL_CALLBACK . 'qq',
    ),
    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'liuniukeji', //默认数据加密KEY

    /* 调试配置 */
    'SHOW_PAGE_TRACE' => false,

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 2, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符
    'URL_HTML_SUFFIX'      => '', // 伪静态


    // /* 数据库配置 */
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'baishouka', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'ln_', // 数据库表前缀


    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__UPLOADS__' => __ROOT__ . '/'. APP_NAME . '/Uploads/',
        '__PUBLIC_IMG__'    => __ROOT__ . '/Public/Static/images',
        '__CORE__'     => __ROOT__ . '/'. APP_NAME . '/Core/Static',
        '__HOME__'     => __ROOT__ . '/'. APP_NAME . '/Home/Static',
        '__MOBILE__'     => __ROOT__ . '/'. APP_NAME . '/Mobile/Static',
        '__PUBLIC__' => '/Public/',
    ),

    //文件上传路径
    'UPLOAD_ROOTPATH' => './Uploads/',

    //是否开启session
    'SESSION_AUTO_START' => true,

    /*图片上传*/
    'IMAGE_MAXSIZE' => '10M',
    'ALLOW_IMG_EXT' => array('jpg', 'pjpeg', 'bmp', 'gif', 'png', 'jpeg'),

    /*视频上传*/
//    'VIDEO_MAXSIZE' => '100M',
    'VIDEO_MAXSIZE' => 0,
    'ALLOW_VIDEO_EXT' => array('mp4','wma'),
    /*声音上传*/
    'VOICE_MAXSIZE' => '10M',
    'ALLOW_VOICE_EXT' => array('mp3'),

    /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 20*1024*1024, //上传文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Picture/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件的命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）
    'LOAD_EXT_CONFIG'       =>  'oauth',         //加载网站设置文件

    /* 附件上传相关配置 */
    'FIELD_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 0, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'rar,zip,mp4,wma', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/UploadsField/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）
    'UPLOAD_FIELD_ROOT' => '/Uploads/UploadsField',


    // 前台所用图片上传目录
    'UPLOAD_PICTURE_ROOT' => '/Uploads/Picture',

    'PAGE_SIZE' => 10, //page_size


    // 极光推送
    'USER_PUSH_APIKEY'    => 'xxxxxx',
    'USER_PUSH_SECRETKEY' => 'xxxxxxx',

       //发送邮箱验证码的配置文件
    'EMAIL_SMTP' => 'smtp.163.com',
    'EMAIL_USERNAME' => 'admin@shoukb.com',
    'EMAIL_PASSWORD' => 'xxxxx' ,
    'EMAIL_FROM_NAME' => '收卡网',

    

    'TAGLIB_LOAD'   => true,
    'APP_AUTOLOAD_PATH'  =>'@.TagLib',
    'TAGLIB_BUILD_IN'  =>  'cx,cms',
    'DEFAULT_FILTER'    => 'htmlspecialchars',


    define('MOBILE_URL','http://10.168.1.254'),

);
