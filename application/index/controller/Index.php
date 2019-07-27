<?php
namespace app\index\controller;
use think\Controller;
use think\Image;
use think\Db;
use Zxing\QrReader;
use phpqrcode\QRcode;

/**
 * 首页控制器
 * https://qr.52ecy.cn
 */
class Index extends Controller{

    /**
     * 图片目录
     * @var [type]
     */
    private $path = ROOT_PATH . 'public/static/images/';

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
        ini_set('memory_limit','1024M');
        $path = $this->path . 'temp/';
        $file = request()->file('file');
        $info = $file
            ->rule(function(){
                return md5(microtime(true));
            })
            ->validate(['size'=>2097152,'ext'=>'jpg,png,jpeg,bmp'])
            ->move($path);
        if($info){
            $name = $info->getFilename(); 
            unset($info);
            $qrcode = new QrReader($path.$name); 
            $url = $qrcode->text(); 
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
     * 生成二维码
     * @return [type] [description]
     */
    public function make(){
        $file_name = md5(time()).'.png';
        $file = $this->path .'qr/' . $file_name;
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

        QRcode::png(url('index/index/qr','id='.$id,'html',true),$file,'L', 18, 2);
        $font_path = ROOT_PATH.'public/static/font/HYQingKongTiJ.ttf';
        $image = Image::open($this->path.'template/qr.png');
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
        if(!is_file($this->path . 'qr/' . $res['name'])){
            abort(404,'二维码不存在');
        }
        $ua = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($ua, 'MicroMessenger')) {
            $this->assign([
                'pay_url' => $res['wechat'],
                'pay_name' => $res['name']
            ]);
            return $this->fetch('wx');
        } elseif (strpos($ua, 'AlipayClient')) {
            header('location: ' . $res['alipay']);
            exit();
        } elseif (strpos($ua, 'QQ/')) {
            $this->assign([
                'pay_url' => urlencode($res['qq']),
                'pay_name' => $res['name']
            ]);
            return $this->fetch('qq');
        } else {
            $this->assign('url',$res['qr']);
            return $this->fetch('qr');
        }
    }


    /*
     * 二维码生成
     */
    public function api($text=''){
        if($text==''){
            return json([ 'status' => 1, 'msg' => '请输入内容']);
        }
        ob_clean();
        QRcode::png($text,false, 'L',5, 2);  
        $content = ob_get_clean();
        return response($content,200,[
            'Content-length'=>strlen($content)
        ])->contentType('image/png');
    }
    
}
