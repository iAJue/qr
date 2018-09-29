<?php
namespace app\index\controller;
use think\Controller;
use think\Image;
use think\Db;


/**
 * 首页控制器
 * https://qr.52ecy.cn
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
        $number = Db::table('qr')
            ->sum('number');
        $this->assign([
            'count' => $count,
            'total' => $total,
            'number' => $number
        ]);
        return $this->fetch();
    }

    /**
     * 二维码上传
     * @return [type] [description]
     */
    public function uploads($pay){
        $path = ROOT_PATH . 'public/static/';
        $file = request()->file('file');
        $info = $file
            ->rule(function(){
                return md5(microtime(true));
            })
            ->validate(['size'=>2097152,'ext'=>'jpg,png,gif'])
            ->move($path);
        if($info){
            $name = $info->getFilename(); 
            unset($info);
            @$url = file_get_contents(config('distinguish').config('view_replace_str.__PUBLIC__').$name);
            @unlink($path.$name);
            if (!$url) {
                return [ 'status' => 1, 'msg' => '二维码识别失败' ];
            }
            if($pay == 'alipay' && stripos ($url,'ALIPAY.COM') === false) {
                return [ 'status' => 1, 'msg' => '请上传正确的支付宝收款码'];
            }elseif ($pay == 'qq' && stripos($url,'qianbao.qq') === false) {
                return [ 'status' => 1, 'msg' => '请上传正确的QQ收款码'];
            }elseif ($pay == 'wechat' && (stripos($url,'wxp://') === false && stripos($url,'wx.tenpay.com') === false)) {
                return [ 'status' => 1, 'msg' => '请上传正确的微信收款码'];
            }
            return [ 'status' => 0, 'msg' => $url];
        }
        return [ 'status' => 1, 'msg' => $file->getError()];
    }

    /**
     * 解析二维码(废弃)
     * @return [type] [description]
     */
    private function analysis($file,$multipart = true){
        $ch = curl_init();
        if ($multipart) {
            $data = ['file' => new \CURLFile(realpath($file))];
        }else{
            $data = ['charset' => 'UTF-8','url' => $file];
        }
        curl_setopt($ch, CURLOPT_URL, config('distinguish'));
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
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
        // 这里还可以对提交的数据再次效验
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
        // 有5%的几率会导致生成失败，这里可以加个大小判断
        $data = curl_get_https(config('generate').url('index/index/qr','id='.$id,'html',true));
        if(strlen($data)<10000){ 
            return [ 'status' => 1, 'msg' => '生成失败，请稍后重试'];
        }
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
        Db::table('qr')
            ->where('id',$id)
            ->setInc('number');
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
            exit();
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
