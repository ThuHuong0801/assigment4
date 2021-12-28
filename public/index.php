<?php
// include các file cần thiết (nếu có)
// get URL ở đây
spl_autoload_register(callback: 'autoloader');
function autoloader($class)
{
    require_once '../' . str_replace('\\','/', $class) . '.php';
}
/**
 * ***************** CÁCH VIẾT CODE TEST *******************
 *
 * Với phần router test
 * *** Sử dụng class Router để bóc tách các thành phần từ URL đã get phía trên (controller - action - param)
 * *** Sử dụng include_once (require_once) để include controller được gọi đến
 * *** Tạo controller instance và gọi đến action tương ứng để show ra màn hình kết quả
 *
 * Với phần DB test
 * *** Trong action của controller vừa truy cập, viết logic code xử lý để gọi đến các method của DB.php
 * *** Demo được fetch method và execute method
 *
 */

$app = new \app\Web();
?>