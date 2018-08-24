<?php

/**
 * 收款啦应用程序配置
 * https://qr.52ecy.cn
 */
return [

	// 应用调试模式
	// 'app_debug'              => true,

	// 视图输出字符串内容替换
	'view_replace_str'       => [
		//修改成自己的域名
	    '__PUBLIC__' => 'https://qr.52ecy.cn/static/',
	],

	// 站点标题
	'title' => '收款啦',
	
	// 站点副标题
	'siteinfo' => '',

	// 描述
	'description' => '',

	// 关键字
	'keywords' => '',

	// 二维码识别接口
	'distinguish' => 'https://www.sojson.com/qrcode/deqrByImages.shtml',

	// 识别结果截取正则
	'reg' => '/{"txt":"(.*?)","/',

	// 二维码生成接口
	'generate' => 'http://qr.liantu.com/api.php?w=600&text=',

	// 导航
	'link' => [
		'如果可以，请给作者一份打赏' => 'https://www.52ecy.cn/post-87.html',
		'GitHub' => 'https://github.com/178146582/qr',
		'博客' => 'https://www.52ecy.cn',
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

