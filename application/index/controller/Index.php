<?php
namespace app\index\controller;
use think\Controller;
use think\Image;
use think\Db;


/**
 * 首页控制器
 */
class Index extends Controller{

	/**
	 * 万能码路径
	 * @var [type]
	 */
	private $path = ROOT_PATH . 'public/static/images/qr/';

	/**
	 * 首页
	 * @return [type] [description]
	 */
    public function index(){
    	$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
    	$count = Db::table('qr')
    		->where('time','>',$beginToday)
    		->where('time','<',time())
    		->count();
    	$total = Db::table('qr')
    		->count();
    	$this->assign([
    		'count' => $count,
    		'total' => $total
    	]);
    	return $this->fetch();
    }

    /**
     * 二维码上传
     * @return [type] [description]
     */
    public function uploads($pay){

        $file = request()->file('file');
        $info = $file
            ->rule(function(){
                return md5(microtime(true));
            })
            ->validate(['size'=>2097152,'ext'=>'jpg,png,gif'])
            ->check();

        if($info){
        	$url = $this->analysis($file->getInfo('filename')['tmp_name']);
    		
        	if (!$url) {
        		return [ 'status' => 1, 'msg' => '二维码识别失败' ];
        	}
    		if($pay == 'alipay' && stripos ($url,'ALIPAY.COM') === false) {
    			return [ 'status' => 1, 'msg' => '请上传正确的支付宝收款码'];
    		}elseif ($pay == 'qq' && stripos($url,'qianbao.qq') === false) {
    			return [ 'status' => 1, 'msg' => '请上传正确的QQ收款码'];
    		}elseif ($pay == 'wechat' && stripos($url,'wxp://') === false) {
    			return [ 'status' => 1, 'msg' => '请上传正确的微信收款码'];
    		}
        	
    		return [ 'status' => 0, 'msg' => $url];
        }
        return [ 'status' => 1, 'msg' => $file->getError()];
    }

    /**
     * 解析二维码
     * @return [type] [description]
     */
    private function analysis($file){
		$ch = curl_init();
		$data = ['file' => new \CURLFile(realpath($file))];
		curl_setopt($ch, CURLOPT_URL, config('distinguish'));
		curl_setopt($ch, CURLOPT_POST, 1 );
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT,"TEST");
		$result = curl_exec($ch);
		// var_dump($result);
        preg_match(config('reg'), $result, $match);
        if (isset($match[1])) {
        	return stripslashes($match[1]);
        }
        return false;
    }

    /**
     * 生成二维码
     * @return [type] [description]
     */
    public function make(){
    	$file_name = md5(time()).'.png';
    	$file = $this->path . $file_name;
    	$name = input('post.name');
        $name = $name=='' ? config('title','收款啦'): $name;
    	$len = mb_strlen($name, 'UTF-8');
    	if ($len > 5) {
    		return [ 'status' => 1, 'msg' => '昵称最大限制5个字数'];
    	}
    	$id = Db::table('qr')
    		->insertGetId([
    			'qr' => $file_name,
    			'alipay' => input('post.alipay'),
    			'qq' => input('post.qq'),
    			'wechat' => input('post.wechat'),
    			'time' => time(),
    			'name' => $name,
    			'ip' => get_client_ip()
    		]);
    	$data = curl_get_https(config('generate').url('index/index/qr','id='.$id,'html',true));
    	file_put_contents($file, $data);
    	$font_path = ROOT_PATH.'public/static/font/HYQingKongTiJ.ttf';
    	$image = Image::open($this->path.'qr.png');
    	$image->water($file,[150,200])->save($file);
    	$image->text('扫码向 “'.$name.'” 付款',$font_path,50,'#ffffff',[200-($len-1)*30,60])->save($file);
    	$image->text(config('title'),$font_path,60,'#5A91EB',[350,1115])->save($file);
    	return [ 'status' => 0, 'msg' => $file_name];
    }

    /**
     * 二维码展示
     * @return [type] [description]
     */
    public function qr($id){

    	$res = Db::table('qr')
    		->where('id',$id)
    		->find();
    	if (!$res) {
    		abort(404,'页面不存在');
    	}
    	$ua = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($ua, 'MicroMessenger')) {
		    $this->assign([
		    	'pay_url' => 'http://qr.liantu.com/api.php?text='.$res['wechat'],
		    	'pay_img' => 'mmexport1534834391879.jpeg',
		    	'pay_name' => $res['name']
		    ]);
			return $this->fetch('wx');
		} elseif (strpos($ua, 'AlipayClient')) {
		    header('location: ' . $res['alipay']);
		} elseif (strpos($ua, 'QQ/')) {
		    $this->assign([
		    	'pay_url' => 'http://qr.liantu.com/api.php?text='.urlencode($res['qq']),
		    	'pay_img' => 'Cache_24736d9b21c67e31.jpg',
		    	'pay_name' => $res['name']
		    ]);
			return $this->fetch('qq');
		} else {
			$this->assign('url',$res['qr']);
		    return $this->fetch('qr');
		}
    }
    
}
