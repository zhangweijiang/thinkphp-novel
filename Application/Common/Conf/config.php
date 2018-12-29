<?php
// +----------------------------------------------------------------------
// | 系统总配置
// +----------------------------------------------------------------------
return array(
    //'配置项'=>'配置值'
    //插件预加载地址
    //'AUTOLOAD_NAMESPACE' => array('Addons' => './Addons/'),
    // 允许访问的模块列表
    'MODULE_ALLOW_LIST' => array( 'Admin', 'Pc'), //允许访问的模块
    //'DEFAULT_MODULE' => 'Admin', // 默认模块
	'DEFAULT_MODULE' => 'Pc', // 默认模块
    /* 模板引擎设置 */
    'VIEW_PATH' => './Tpl/', //默认系统模版路径
    'TMPL_CONTENT_TYPE' => 'text/html', // 默认模板输出类型
    'TMPL_ACTION_ERROR' => THINK_PATH . 'Tpl/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => THINK_PATH . 'Tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE' => THINK_PATH . 'Tpl/think_exception.tpl', // 异常页面的模板文件
    'TMPL_DETECT_THEME' => false, // 自动侦测模板主题
    'TMPL_TEMPLATE_SUFFIX' => '.html', // 默认模板文件后缀
    'TMPL_FILE_DEPR' => '_', //模板文件CONTROLLER_NAME与ACTION_NAME之间的分割
    'TMPL_PARSE_STRING' => array(
        '__UPLOAD__' => __ROOT__ . '/Upload',
    ),

    /* URL设置 */
    'URL_CASE_INSENSITIVE' => true, // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL' => 2, // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式
    'URL_PATHINFO_DEPR' => '/', // PATHINFO模式下，各参数之间的分割符号
    'URL_HTML_SUFFIX' => '', //URL伪静态后缀

    /* 数据库设置 */
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => 'novel', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'root', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'hxb_', // 数据库表前缀

    'AUTOLOAD_NAMESPACE' => array(
        'Addons' => ADDON_PATH, //自动加载命名空间
    ),


    //邮件配置
    'THINK_EMAIL' => array(

        'SMTP_HOST' => 'smtp.qq.com', //SMTP服务器

        'SMTP_PORT' => '465', //SMTP服务器端口

        'SMTP_USER' => '982215226@qq.com', //SMTP服务器用户名

        'SMTP_PASS' => 'qwgoicxmutkobehg', //SMTP服务器密码

        'FROM_EMAIL' => '982215226@qq.com',

        'FROM_NAME' => '黄晓滨', //发件人名称

        'REPLY_EMAIL' => '982215226@qq.com', //回复EMAIL（留空则为发件人EMAIL）

        'REPLY_NAME' => '', //回复名称（留空则为发件人名称）

        'SESSION_EXPIRE'=>'72',
    ),

);