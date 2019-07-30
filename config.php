<?php

/**
 * 收款啦应用程序配置
 * https://qr.52ecy.cn
 */
return [

	// 应用调试模式
	//'app_debug'              => true,

	// 视图输出字符串内容替换
	'view_replace_str'       => [
		//修改成自己的 " 域名 "
	    '__PUBLIC__' => 'http://www.baidu.com/static/',
	],

	// 站点标题
	'title' => '收款啦',
	
	// 站点副标题
	'siteinfo' => '',

	// 描述
	'description' => '',

	// 关键字
	'keywords' => '',

	// 导航
	'link' => [

		'赞助项目' => 'https://pay.52ecy.cn/',

		// 可以参考底部评论
		'帮助' => 'https://www.52ecy.cn/post-88.html',
		// 随时可能更新
		'GitHub' => 'https://github.com/iAjue/qr',

		'博客' => 'https://www.52ecy.cn',
		// 有问题请加群
		'有问题点我' => 'http://shang.qq.com/wpa/qunwpa?idkey=826e8e5961b8acf3eb7bb4fd8595a59e38deb618deaee70912dd0c4cd9f97457',
	],
	
	//是否开启站点统计 on/off
	'statis' => 'on', 

	//是否显示底部一堆废话 on/off
	'explain' => 'on',

	// 底部信息 支持HTML代码
	'footerinfo' => '',

	//保留它是对作者的最大支持
	'copyright' => 'Powered by <a target="_blank" href="https://qr.52ecy.cn">收款啦三合一平台</a>'
];

