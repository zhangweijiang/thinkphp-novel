<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境

if (version_compare(PHP_VERSION, '5.3.0', '<')) die('require PHP > 5.3.0 !');

if (ini_get('magic_quotes_gpc')) {
    function stripslashesRecursive(array $array){
        foreach ($array as $k => $v) {
            if (is_string($v)){
                $array[$k] = stripslashes($v);
            } else if (is_array($v)){
                $array[$k] = stripslashesRecursive($v);
            }
        }
        return $array;
    }
    $_GET = stripslashesRecursive($_GET);
    $_POST = stripslashesRecursive($_POST);
}

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);

// 定义应用目录
define('APP_PATH', './Application/');
define('ROOT_PATH', realpath('./') . DIRECTORY_SEPARATOR);
define('ROOT_URL', rtrim(dirname($_SERVER["SCRIPT_NAME"]), '\\/') . '/');
define('CONFIG_PATH', ROOT_PATH . 'Public/Config/');
define('ADDON_PATH','Addons');//严格要求
define('DATA_PATH', './Data/');
defined('UPLOAD_PATH')   or define('UPLOAD_PATH',     ROOT_PATH .'Upload/'); // 图片上传路径
defined('PUBLIC_URL')   or define('PUBLIC_URL',     ROOT_URL .'Statics/'); // 样式访问路径
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单