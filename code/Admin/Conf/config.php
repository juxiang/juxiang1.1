<?php

$siteconfig = require './Admin/siteconfig.inc.php';
$config = array(
    /*
     * 0:普通模式 (采用传统癿URL参数模式 )
     * 1:PATHINFO模式(http://<serverName>/appName/module/action/id/1/)
     * 2:REWRITE模式(PATHINFO模式基础上隐藏index.php)
     * 3:兼容模式(普通模式和PATHINFO模式, 可以支持任何的运行环境, 如果你的环境不支持PATHINFO 请设置为3)
     */
    'URL_MODEL' => 3,
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'huaxianyun.mysql.rds.aliyuncs.com',
    'DB_NAME' => 'huaxianyun',
    'DB_USER' => 'huaxianyun',
    'DB_PWD' => 'huaxianyun',
	'DB_PREFIX' => 'wx_',
	'DB_PORT' => '3306',
	//'DB_DEPLOY_TYPE' => 1,
	//'DB_RW_SEPARATE'=>ture,
	/* 'DB_HOST' => '127.0.0.1',
    'DB_NAME' => 'phones',
    'DB_USER' => 'root',
    'DB_PWD' => '1314',
    'DB_PREFIX' => 'wx_',
    'DB_PORT' => '3306', */
    'APP_AUTOLOAD_PATH' => '@.TagLib',
    'SESSION_AUTO_START' => true,
    'VAR_PAGE' => 'pageNum',
    'URL_HTML_SUFFIX' => '',//URL静态后缀名为空
    'PAGE_NUM_SHOWN' => 10, //分页 页标数字多少个
    'NUM_PER_PAGE' => 20,
    'USER_AUTH_ON' => true,
    'USER_AUTH_TYPE' => 1, // 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY' => 'authId', // 用户认证SESSION标记
    'ADMIN_AUTH_KEY' => 'administrator',
    'USER_AUTH_MODEL' => 'User', // 默认验证数据表模型
    'AUTH_PWD_ENCODER' => 'md5', // 用户认证密码加密方式
    'USER_AUTH_GATEWAY' => '/Public/login', // 默认认证网关
    'NOT_AUTH_MODULE' => 'Public', // 默认无需认证模块
    'REQUIRE_AUTH_MODULE' => '', // 默认需要认证模块
    'NOT_AUTH_ACTION' => '', // 默认无需认证操作
    'REQUIRE_AUTH_ACTION' => '', // 默认需要认证操作
    'GUEST_AUTH_ON' => false, // 是否开启游客授权访问
    'GUEST_AUTH_ID' => 0, // 游客的用户ID
    'DB_LIKE_FIELDS' => 'title|remark',
    'RBAC_ROLE_TABLE' => 'wx_role',
    'RBAC_USER_TABLE' => 'wx_role_user',
    'RBAC_ACCESS_TABLE' => 'wx_access',
    'RBAC_NODE_TABLE' => 'wx_node',
	'TMPL_PARSE_STRING'=>array(           //添加自己的模板变量规则
			'__CSS__'=>__ROOT__.'/Public/Css',
			'__JS__'=>__ROOT__.'/Public/Js',
	),
);

return array_merge($config, $siteconfig);
?>
