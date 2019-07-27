<?php



function create($url=''){  
    require_once 'phpqrcode.php';  
    $errorCorrectionLevel = 'L';   
    $matrixPointSize = 5;       
    $QR = QRcode::png($url,false,$errorCorrectionLevel, $matrixPointSize, 2);  
}  


create('https://i.qianbao.qq.com/wallet/sqrcode.htm?m=tenpay&f=wallet&a=1&ac=CAEQ0cKUjQYYg-fn6AU%3D_xxx_sign&u=1638211921&n=%E9%AB%98%E5%9D%82%E6%A1%90%E4%B9%83'); 
